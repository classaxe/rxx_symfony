<?php
namespace App\Repository;

use App\Columns\Signals as SignalsColumns;
use App\Columns\SignalLogs as SignalLogsColumns;
use App\Columns\SignalListeners as SignalListenersColumns;
use App\Entity\Signal;
use App\Utils\Rxx;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use Psr\Log\LoggerInterface;

class SignalRepository extends ServiceEntityRepository
{
    private $debug = 0;  // 1 = log, 2 = screen

    const dateFields = [
        'logged_date_1',
        'logged_date_2',
        'logged_first_1',
        'logged_first_2',
        'logged_last_1',
        'logged_last_2'
    ];
    const defaultlimit =    50;
    const defaultOrder =    'a';
    const defaultPage =     0;
    const defaultSorting =  'khz';
    const regions = ['AF', 'AN', 'AS', 'CA', 'EU', 'IW', 'NA', 'OC', 'SA'];
    const withinPeriods = [
        '1 Month' =>    0.0833,
        '2 Months' =>   0.1667,
        '3 Months' =>   0.25,
        '6 Months' =>   0.5,
        '9 Months' =>   0.75,
        '1 Year' =>     1,
        '2 Years' =>    2,
        '3 Years' =>    3,
        '4 Years' =>    4,
        '5 Years' =>    5,
        '10 Years' =>   10,
        '15 Years' =>   15
    ];

    const collapsable_sections = [
        'loggings' => [
            'rww_focus',
            'heard_in',
            'heard_in_mod',
            'listener',
            'listener_invert',
            'listener_filter',
            'logged_date_1',
            'logged_date_2',
            'logged_first_1',
            'logged_first_2',
            'logged_last_1',
            'logged_last_2'
        ],
        'customise' => [
            'personalise',
            'hidenotes',
            'morse',
            'offsets',
            'range_gsq',
            'range_min',
            'range_max'
        ]
    ];

    private $args;
    private $connection;
    private $query = [
        'from' =>   [],
        'having' => [],
        'limit' =>  [],
        'order' =>  [],
        'param' =>  [],
        'select' => [],
        'where' =>  [],
    ];
    private $logRepository;
    /** @var LoggerInterface */
    private $logger;
    private $signalsColumns;
    private $signalListenersColumns;
    private $signalLogsColumns;
    private $system;

    private $tabs = [
        [ 'signal', 'Profile' ],
        [ 'signal_listeners', 'Listeners (%d)' ],
        [ 'signal_logs', 'Logs (%d)' ],
        [ 'signal_map' , 'Map'],
        [ 'signal_rx_map_na' , 'Reception Map (NA)'],
        [ 'signal_rx_map_eu' , 'Reception Map (EU)'],
        [ 'signal_weather', 'Weather' ],
    ];
    /**
     * SignalRepository constructor.
     * @param ManagerRegistry $registry
     * @param Connection $connection
     * @param LogRepository $logRepository
     * @param SignalsColumns $signalsColumns
     * @param SignalListenersColumns $signalListenersColumns
     * @param SignalLogsColumns $signalLogsColumns
     */
    public function __construct(
        ManagerRegistry $registry,
        Connection $connection,
        LogRepository $logRepository,
        LoggerInterface $logger,
        SignalsColumns $signalsColumns,
        SignalListenersColumns $signalListenersColumns,
        SignalLogsColumns $signalLogsColumns
    ) {
        parent::__construct($registry, Signal::class);
        $this->connection = $connection;
        $this->logRepository = $logRepository;
        $this->logger = $logger;
        $this->signalsColumns = $signalsColumns->getColumns();
        $this->signalListenersColumns = $signalListenersColumns->getColumns();
        $this->signalLogsColumns = $signalLogsColumns->getColumns();
    }

    private function _addFilterActive()
    {
        if (isset($this->args['active'])) {
            switch($this->args['active']) {
                case '': // 'All'
                    $this->query['where'][] ='(s.decommissioned = 0)';
                    break;
                case '1': // Active
                    $this->query['where'][] ='(s.active = 1)';
                    break;
                case '2': // Inactive
                    $this->query['where'][] ='(s.active = 0)';
                    break;
                case '3': // Decommissioned
                    $this->query['where'][] ='(s.decommissioned = 1)';
                    break;
                case '4': // All inc Decomm
                    // Allow everything
                    break;
            }
        }
        return $this;
    }

    private function _addFilterCall()
    {
        if ($this->args['call'] ?? '') {
            $this->query['where'][] ='s.call LIKE :like_call';
            $this->query['param']['like_call'] = '%' . htmlentities($this->args['call']) . '%';
        }
        return $this;
    }

    private function _addFilterChannels()
    {
        if ($this->args['channels'] ?? false) {
            $this->query['where'][] = $this->_getFilterForChannels($this->args['channels']);
        }
        return $this;
    }

    private function _addFilterFreq()
    {
        $khz_1 = (float)($this->args['khz_1'] ?? 0) ? (float)$this->args['khz_1'] : 0;
        $khz_2 = (float)($this->args['khz_2'] ?? 0) ? (float)$this->args['khz_2'] : 1000000;

        if ($khz_1 !== 0 || $khz_2 !== 1000000) {
            $this->query['where'][] ='(s.khz BETWEEN :khz1 AND :khz2)';
            $this->query['param']['khz1'] = $khz_1;
            $this->query['param']['khz2'] = $khz_2;
        }
        return $this;
    }

    private function _addFilterGsq()
    {
        if ($this->args['gsq'] ?? false) {
            $gsq = explode(" ", str_replace('*', '%', $this->args['gsq']));
            $in = $this->_buildInParamsList('gsq', $gsq, '', '%');
            $this->query['where'][] = "s.gsq LIKE " . implode(" OR s.gsq LIKE ", $in);
        }
        return $this;
    }

    private function _addFilterHeardIn()
    {
        if ($this->args['heard_in'] ?? false) {
            $heard_in_arr = explode(' ', $this->args['heard_in']);
            if ($this->args['heard_in_mod'] === '') {
                $in = $this->_buildInParamsList('heard_in', $heard_in_arr);
                $this->query['where'][] =
                    "l.heard_in IN (" . implode(', ', $in) . ")";
            } else {
                $in = $this->_buildInParamsList('heard_in', $heard_in_arr, '%', '%');
                $this->query['where'][] =
                    "s.heard_in LIKE " . implode(' AND s.heard_in LIKE ', $in);
            }
        }
        return $this;
    }

    private function _addFilterListeners()
    {
        if (!isset($this->args['listener']) || !isset($this->args['listener_invert'])) {
            // Params not available
            return $this;
        }
        if ((!$this->args['listener'] || in_array('', $this->args['listener'])) && !$this->args['listener_invert']) {
            // 'Logged by' and 'Anyone'
            return $this;
        }
        if ((!$this->args['listener'] || in_array('', $this->args['listener'])) && $this->args['listener_invert']) {
            // 'Not Logged by' and 'Anyone'
            $this->query['where'][] = "0 = 1 /* Showing 'All Listeners' but NOT logged by */";
            return $this;
        }
        $in = $this->_buildInParamsList('heard_by', $this->args['listener']);
        if (!$this->args['listener_invert']) {
            $this->query['where'][] = "l.listenerId IN (" . implode(',', $in) . ")";
            return $this;
        }
        $this->query['where'][] =
            "s.id NOT IN(
                SELECT
                   DISTINCT s2.id
                FROM
                   signals s2
                INNER JOIN logs l2 ON
                   s2.id = l2.signalID
                WHERE
                   l2.listenerID IN(" . implode(',', $in) . ")
            )";
        return $this;
    }

    private function _addFilterLoggedDate()
    {
        if (($this->args['logged_date_1'] ?? false) || ($this->args['logged_date_2'] ?? false)) {
            $this->query['where'][] =
                "(SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date BETWEEN :logged_date_1 AND :logged_date_2) > 0";
                //"l.date between :logged_date_1 AND :logged_date_2";
            $this->query['param']['logged_date_1'] = (
                $this->args['logged_date_1'] ? $this->args['logged_date_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_date_2'] = (
                $this->args['logged_date_2'] ? $this->args['logged_date_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function _addFilterLoggedFirst()
    {
        if (($this->args['logged_first_1'] ?? false) || ($this->args['logged_first_2'] ?? false)) {
            $this->query['where'][] = "s.first_heard between :logged_first_1 AND :logged_first_2";
            $this->query['param']['logged_first_1'] = (
                $this->args['logged_first_1'] ? $this->args['logged_first_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_first_2'] = (
                $this->args['logged_first_2'] ? $this->args['logged_first_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function _addFilterLoggedLast()
    {
        if (($this->args['logged_last_1'] ?? false) || ($this->args['logged_last_2'] ?? false)) {
            $this->query['where'][] = "s.last_heard between :logged_last_1 AND :logged_last_2";
            $this->query['param']['logged_last_1'] = (
                $this->args['logged_last_1'] ? $this->args['logged_last_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_last_2'] = (
                $this->args['logged_last_2'] ? $this->args['logged_last_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function _addFilterNotes()
    {
        if ($this->args['notes'] ?? '') {
            $this->query['where'][] ='s.notes LIKE :like_notes';
            $this->query['param']['like_notes'] = '%' . htmlentities($this->args['notes']) . '%';
        }
        return $this;
    }

    private function _addFilterRange()
    {
        if ($this->args['range_gsq'] ?? '' && $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])) {
            $min = (float)$this->args['range_min'] ? (float)$this->args['range_min'] : 0;
            $max = (float)$this->args['range_max'] ? (float)$this->args['range_max'] : 1000000;
            $mult = $this->args['range_units'] ==='km' ? RXX::DEG_KM_MULTIPLIER : RXX::DEG_MI_MULTIPLIER;

            if ($min !== 0 || $max !== 1000000) {
                if ($this->args['range_gsq'] !== '' &&
                    $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
                ) {
                    $this->query['where'][] =
                        "s.lat != 0 AND s.lon != 0) AND\n"
                        . "    (CAST(\n"
                        . "      COALESCE(\n"
                        . "        ROUND(\n"
                        . "          DEGREES(\n"
                        . "            ACOS(\n"
                        . "              (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                        . "                (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                        . "              )\n"
                        . "            ) * ".$mult.",\n"
                        . "          2\n"
                        . "        ), ''\n"
                        . "      ) AS UNSIGNED\n"
                        . "    ) BETWEEN :min AND :max";
                    $this->query['param']['lat'] = $lat_lon['lat'];
                    $this->query['param']['lon'] = $lat_lon['lon'];
                    $this->query['param']['min'] = $min;
                    $this->query['param']['max'] = $max;
                }
            }
        }
        return $this;
    }

    private function _addFilterRecently()
    {
        if (($this->args['recently'] ?? false) && ($this->args['within'] ?? false)) {
            $period = ((float) $this->args['within'] >= 1 ?
                (int) $this->args['within'] . " year"
              :
                round($this->args['within'] * 12) . " month"
            );
            $within = DateTime::createFromFormat('Y-m-d', date('Y-m-d'))
                ->modify('-' . $period)
                ->format('Y-m-d');
            if ($this->args['recently'] === 'logged') {
                $this->query['where'][] = "s.last_heard > :within";
                $this->query['param']['within'] = $within;
            };
            if ($this->args['recently'] === 'unlogged') {
                $this->query['where'][] = "s.last_heard < :within";
                $this->query['param']['within'] = $within;
            };
        }
        return $this;
    }

    private function _addFilterRegion()
    {
        if ($this->args['region'] ?? false) {
            $this->query['where'][] = '(s.region = :region)';
            $this->query['param']['region'] = $this->args['region'];
        }
        return $this;
    }

    private function _addFilterRwwFocus()
    {
        if ('rww' === $this->system && isset($this->args['rww_focus']) && $this->args['rww_focus']) {
            $this->query['where'][] ='s.heard_in_' . $this->args['rww_focus']. ' = 1';
        }
    }

    private function _addFilterStatesAndCountries()
    {
        $clauses = [];
        if ($this->args['countries'] ?? false) {
            $countries = explode(" ", str_replace('*', '%', $this->args['countries']));
            $in = $this->_buildInParamsList('countries', $countries);
            $clauses[] = "(s.itu LIKE " . implode(" OR s.itu LIKE ", $in) . ")";
        }
        if ($this->args['states'] ?? false) {
            $states = explode(" ", str_replace('*', '%', $this->args['states']));
            $in = $this->_buildInParamsList('states', $states);
            $clauses[] = "(s.sp LIKE " . implode(" OR s.sp LIKE ", $in) . ")";
        }
        switch (count($clauses)) {
            case 0:
                break;
            case 1:
                $this->query['where'][] = $clauses[0];
                break;
            case 2:
                $this->query['where'][] =
                    $clauses[0]
                    . ' ' . (in_array($this->args['sp_itu_clause'], ['AND', 'OR']) ? $this->args['sp_itu_clause'] : 'AND')
                    . ' ' . $clauses[1];
                break;
        }
        return $this;
    }

    private function _addFilterSystem()
    {
        switch ($this->system) {
            case "reu":
                $this->query['where'][] ='s.heard_in_eu = 1';
                break;
            case "rna":
                $this->query['where'][] ='(s.heard_in_na = 1 OR s.heard_in_ca = 1)';
                break;
            case "rww":
                $this->query['where'][] = "s.logs > 0";
                break;
        }
        return $this;
    }

    private function _addFilterStatus()
    {
        if (isset($this->args['status'])) {
            $filter = $this->_getFilterForStatus($this->args['status']);
            if ($filter) {
                $this->query['where'][] = $filter;
            }
        }
        return $this;
    }

    private function _addFilterTypes()
    {
        $this->query['where'][] = $this->_getFilterForTypes($this->args['signalTypes']);
        return $this;
    }

    private function _addFilterUnlogged()
    {
        $this->query['where'][] =
            '(s.heard_in_af = 0 AND s.heard_in_an = 0 AND s.heard_in_as = 0 AND s.heard_in_ca = 0 AND s.heard_in_eu = 0 AND s.heard_in_iw = 0 AND s.heard_in_na = 0 AND s.heard_in_oc = 0 AND s.heard_in_sa = 0)';
        return $this;
    }

    private function _addFromTables()
    {
        if (
            ($this->args['listener'] ?? false) ||
            ($this->args['heard_in'] ?? false) ||
            ($this->args['logged_date_1'] ?? false) ||
            ($this->args['logged_date_2'] ?? false) ||
            ($this->args['logged_first_1'] ?? false) ||
            ($this->args['logged_first_2'] ?? false) ||
            ($this->args['logged_last_1'] ?? false) ||
            ($this->args['logged_last_2'] ?? false)
        ) {
            $this->query['from'][] =
                "signals AS s\n"
                . "INNER JOIN logs AS l ON\n"
                . "    s.id = l.signalID";

        } else {
            $this->query['from'][] =    'signals AS s';
        }
        return $this;
    }

    private function _addLimit($args)
    {
        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $limit =    (int)$args['limit'];
            $offset =   (int)$args['page'] * (int)$args['limit'];
            $this->query['limit'][] = "{$offset}, {$limit}";
        }
        return $this;
    }

    private function _addOrder($field, $dir)
    {
        $this->query['order'][] = "{$field} {$dir}";
        return $this;
    }

    private function _addOrderForListAndMap()
    {
        if (isset($args['isAdmin']) && $args['isAdmin'] && in_array($args['admin_mode'], ['1', '2'])) {
            $this
                ->_addOrderPrioritizeExactCall()
                ->_addOrderPrioritizeSelected();
        } else {
            $this
                ->_addOrderPrioritizeExactCall()
                ->_addOrderPrioritizeActive()
                ->_addOrderPrioritizeSelected();
        }
    }

    private function _addOrderForSeeklist()
    {
        $this
            ->_addOrder('s.itu','ASC')
            ->_addOrder('s.sp', 'ASC')
            ->_addOrder('s.khz', 'ASC')
            ->_addOrder('s.call', 'ASC');
        return $this;
    }

    private function _addOrderPrioritizeActive()
    {
        if (!($this->args['active'] ?? false)) {
            $this->_addOrder('_active','ASC');
        }
        return $this;
    }

    private function _addOrderPrioritizeExactCall()
    {
        if ($this->args['call'] ?? '') {
            $this->_addOrder('_call','DESC');
        }
        return $this;
    }

    private function _addOrderPrioritizeSelected()
    {
        if ($this->signalsColumns[$this->args['sort']]['sort'] ?? null) {
            $this->_addOrder(
                '_empty',
                'ASC'
            );
            if (in_array($this->args['sort'], ['LSB', 'USB']) && $this->args['offsets'] === '1') {
                $this->_addOrder(
                    ('s.khz ' . ($this->args['sort'] === 'USB' ? '+' : '-') . ' (s.' . $this->args['sort'].'/1000)'),
                    ($this->args['order'] === 'd' ? 'DESC' : 'ASC')
                );
            } else {
                $this->_addOrder(
                    ($this->signalsColumns[$this->args['sort']]['sort']),
                    ($this->args['order'] === 'd' ? 'DESC' : 'ASC')
                );
            }
            if ($this->args['sort'] === 'khz') {
                $this->_addOrder('s.call', 'ASC');
            }
        }
        return $this;
    }

    private function _addSelectColumnMorse()
    {

        if ($this->args['morse'] ?? false === '1') {
            $this->query['select'][] = 's.call AS morse';
        } else {
            $this->query['select'][] = "'' as morse";
        }
        return $this;
    }

    private function _addSelectColumnNotes()
    {

        if ($this->args['hidenotes'] ?? '' === '1') {
            $this->query['select'][] = "'' as notes";
        } else {
            $this->query['select'][] = 's.notes as notes';
        }
        return $this;
    }

    private function _addSelectColumnPersonalise()
    {
        if ($this->args['personalise'] ?? false) {
            $this->query['select'][] =
                'IF (s.ID IN(SELECT l.signalID from logs l where l.listenerID = :personalise), 1, 0) AS personalise';
            $this->query['param']['personalise'] = $this->args['personalise'];
        } else {
            $this->query['select'][] = "0 as personalise";
        }
        return $this;
    }

    private function _addSelectColumnRangeDeg()
    {
        if (($this->args['range_gsq'] ?? false) && $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])) {
            $this->query['select'][] = <<< EOD
                IF (s.lat is null or s.lon is null, null, 
                    CAST( #1
                        COALESCE( #2
                            ROUND( #3
                                DEGREES( #4
                                    ATAN2( #5
                                        (SIN(RADIANS(s.lon) - RADIANS(:lon)) * COS(RADIANS(s.lat))),
                                        (COS(RADIANS(:lat)) * SIN(RADIANS(s.lat)) - SIN(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(s.lon) - RADIANS(:lon)))
                                    )
                                ) + 360
                            ) MOD 360,
                            ''
                        ) AS UNSIGNED
                    )
                ) AS range_deg
EOD;
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_deg";
        }
        return $this;
    }

    private function _addSelectColumnRangeKm() {
        if (($this->args['range_gsq'] ?? false) && $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])) {
            $this->query['select'][] = <<< EOD
                IF (s.lat is null or s.lon is null, null, 
                    CAST(
                        COALESCE(
                            ROUND(
                                DEGREES(
                                    ACOS(
                                        (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))
                                    )
                                ) * :KM,
                                2
                            ),
                            ''
                        ) AS UNSIGNED
                    )
                ) AS range_km
EOD;
            $this->query['param']['KM'] = RXX::DEG_KM_MULTIPLIER;
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_km";
        }
        return $this;
    }

    private function _addSelectColumnRangeMiles()
    {
        if (($this->args['range_gsq'] ?? false) && $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])) {
            $this->query['select'][] = <<< EOD
                IF (s.lat is null or s.lon is null, null, 
                    CAST(
                        COALESCE(
                            ROUND(
                                DEGREES(
                                    ACOS(
                                        (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))
                                    )
                                ) * :MI,
                                2
                            ),
                            ''
                        ) AS UNSIGNED
                    )
                ) AS range_mi
EOD;
            $this->query['param']['MI'] = RXX::DEG_MI_MULTIPLIER;
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_mi";
        }
        return $this;
    }

    private function _addSelectColumnsOffsets()
    {
        if ($this->args['offsets'] ?? false === '1') {
            $this->query['select'][] = "IF(s.LSB != 0, CONCAT(COALESCE(s.LSB_approx, ''), ROUND(s.khz - (s.LSB/1000), 3)), '') as LSB";
            $this->query['select'][] = "IF(s.USB != 0, CONCAT(COALESCE(s.USB_approx, ''), ROUND(s.khz + (s.USB/1000), 3)), '') as USB";
        } else {
            $this->query['select'][] = "IF(s.LSB != 0, CONCAT(COALESCE(s.LSB_approx, ''), s.LSB), '') as LSB";
            $this->query['select'][] = "IF(s.USB != 0, CONCAT(COALESCE(s.USB_approx, ''), s.USB), '') as USB";
        }
        return $this;
    }

    private function _addSelectPriotitizeActive()
    {
        if (!($this->args['active'] ?? false)) {
            $this->query['select'][] =
                "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active";
        }
        return $this;
    }

    private function _addSelectPriotitizeExactCall()
    {
        if ($this->args['call'] ?? '') {
            $this->query['select'][] =
                "(CASE WHEN s.call = :call THEN 1 ELSE 0 END) AS _call";
            $this->query['param']['call'] = $this->args['call'];
        }
        return $this;
    }

    private function _addSelectPrioritizeNonEmpty()
    {
        $column = $this->signalsColumns[$this->args['sort']]['sort'] ?? null;
        switch($column) {
            case "" :
                break;
            case "range_deg" :
            case "range_km" :
            case "range_mi" :
                $this->query['select'][] =
                    "(CASE WHEN (s.lat is null or s.lat = 0) AND (s.lon is null or s.lon = 0) THEN 1 ELSE 0 END) AS _empty";
                break;
            case "s.first_heard":
            case "s.last_heard":
                $this->query['select'][] =
                    "(CASE WHEN " . $column . " IS NULL THEN 1 ELSE 0 END) AS _empty";
                break;
            default:
                $this->query['select'][] =
                    "(CASE WHEN " . $column . " = '' OR " . $column . " IS NULL THEN 1 ELSE 0 END) AS _empty";
                break;
        }
        return $this;
    }

    private function _addSelectColumnsAllSignal()
    {
        $distinct = (
            ($this->args['listener'] ?? false) ||
            ($this->args['heard_in'] ?? false) ||
            ($this->args['logged_date_1'] ?? false) ||
            ($this->args['logged_date_2'] ?? false) ||
            ($this->args['logged_first_1'] ?? false) ||
            ($this->args['logged_first_2'] ?? false) ||
            ($this->args['logged_last_1'] ?? false) ||
            ($this->args['logged_last_2'] ?? false)
        );
        $this->query['select'][] =
            ($distinct ? 'DISTINCT ' : '')
            . 's.active,'
            . 's.decommissioned,'
            . 's.type,'
            . 's.ID,'
            . 's.khz,'
            . 's.call,'
            . 's.LSB,'
            . 's.LSB_approx,'
            . 's.USB,'
            . 's.USB_approx,'
            . 's.sec,'
            . 's.format,'
            . 's.QTH,'
            . 's.SP,'
            . 's.ITU,'
            . 's.region,'
            . 's.GSQ,'
            . 's.lat,'
            . 's.lon,'
            . 's.pwr,'
            . 's.heard_in,'
            . 's.heard_in_html,'
            . 's.listeners,'
            . 's.logs,'
            . 's.first_heard,'
            . 's.last_heard'
        ;
        return $this;
    }

    private function _addSelectColumnsAllSignalSeeklist()
    {
        $distinct = (
            ($this->args['listener'] ?? false) ||
            ($this->args['heard_in'] ?? false) ||
            ($this->args['logged_date_1'] ?? false) ||
            ($this->args['logged_date_2'] ?? false) ||
            ($this->args['logged_first_1'] ?? false) ||
            ($this->args['logged_first_2'] ?? false) ||
            ($this->args['logged_last_1'] ?? false) ||
            ($this->args['logged_last_2'] ?? false)
        );
        $this->query['select'][] =
            ($distinct ? 'DISTINCT ' : '')
            . "s.id, s.call, s.decommissioned, s.khz, s.gsq, s.type, s.active, s.sp, s.itu";
        return $this;
    }

    private function _addSelectColumnCountSignal()
    {
        $distinct = (
            ($this->args['listener'] ?? false) ||
            ($this->args['heard_in'] ?? false) ||
            ($this->args['logged_date_1'] ?? false) ||
            ($this->args['logged_date_2'] ?? false) ||
            ($this->args['logged_first_1'] ?? false) ||
            ($this->args['logged_first_2'] ?? false) ||
            ($this->args['logged_last_1'] ?? false) ||
            ($this->args['logged_last_2'] ?? false)
        );
        $this->query['select'][] = "COUNT(" . ($distinct ? "DISTINCT s.id" : "*"). ") AS count";
        return $this;
    }

    private function _buildInParamsList($key, $values, $prefix = '', $suffix = '')
    {
        $in = [];
        if (!$values) {
            return $in;
        }
        foreach($values as $idx => $value) {
            $param = $key . '_' . $idx;
            $in[] = ":" . $param;
            $this->query['param'][$param] = $prefix . $value . $suffix;
        }
        return $in;
    }

    private function _buildQuery()
    {
        $sql =
            "SELECT\n"
            . "    "
            .implode(
                ",\n    ",
                $this->query['select']
            )
            ."\n"
            ."FROM\n"
            ."    "
            .implode(
                ",\n    ",
                $this->query['from']
            )
            ."\n"
            ."WHERE\n"
            . "    ("
            .implode(
                ") AND\n    (",
                $this->query['where']
            )
            .")\n"
            .($this->query['having'] ?
                "HAVING\n"
                . "    ("
                .implode(
                    ") AND\n    (",
                    $this->query['having']
                )
                .")\n"
                : ""
            )
            .($this->query['order'] ? "ORDER BY\n    ".implode(",\n    ", $this->query['order'])."\n" : "")
            .($this->query['limit'] ? "LIMIT\n    ".implode("\n    ", $this->query['limit'])."\n" : "");

        $this->query['from'] =      [];
        $this->query['having'] =    [];
        $this->query['limit'] =     [];
        $this->query['order'] =     [];
        $this->query['select'] =    [];
        $this->query['where'] =     [];

        return $sql;
    }

    private function _getFilterForChannels($channels = false)
    {
        switch ($channels ?? false) {
            case 1:
                return 'MOD(s.khz * 1000, 1000) = 0';
            case 2:
                return 'MOD(s.khz * 1000, 1000) != 0';
            case 3:
                return 'MOD(s.khz * 100, 1000) = 0';
            case 4:
                return 'MOD(s.khz * 100, 1000) != 0';
        }
        return '';
    }

    private function _getFilterForLoggedDates($date_1, $date_2)
    {
        if (($date_1 ?? false) || ($date_2 ?? false)) {
            $query =
                "(SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date BETWEEN :logged_date_1 AND :logged_date_2) > 0";
            $this->query['param']['logged_date_1'] = $date_1 ?? "1900-01-01";
            $this->query['param']['logged_date_2'] = $date_2 ?? "2100-01-01";
            return $query;
        }
        return '';
    }

    private function _getFilterForStatus($status = [])
    {
        if (!in_array('1', $status) && !in_array('2', $status) && !in_array('3', $status)) {
            // NOTHING
            return '(1 = 0)';
        }
        if (!in_array('1', $status) && !in_array('2', $status) && in_array('3', $status)) {
            // DECOM
            return '(s.decommissioned = 1)';
        }
        if (!in_array('1', $status) && in_array('2', $status) && !in_array('3', $status)) {
            // INACTIVE
            return '(s.active = 0 && s.decommissioned = 0)';
        }
        if (!in_array('1', $status) && in_array('2', $status) && in_array('3', $status)) {
            // INACTIVE OR DECOM
            return '(s.active = 0 || s.decommissioned = 1)';
        }
        if (in_array('1', $status) && !in_array('2', $status) && !in_array('3', $status)) {
            // ACTIVE
            return '(s.active = 1)';
        }
        if (in_array('1', $status) && !in_array('2', $status) && in_array('3', $status)) {
            // ACTIVE OR DECOM
            return '(s.active = 1 || s.decommissioned = 1)';
        }
        if (in_array('1', $status) && in_array('2', $status) && !in_array('3', $status)) {
            // ACTIVE OR INACTIVE
            return '(s.decommissioned = 0)';
        }
        if (in_array('1', $status) && in_array('2', $status) && in_array('3', $status)) {
            // ACTIVE OR INACTIVE OR DECOM
            return '';
        }
    }

    private function _getFilterForTypes($types = [])
    {
        $in = $this->_buildInParamsList('type', $types ?? false, '', '');
        return "s.type IN(" . implode(',', $in).")";
    }


    private function _debugQuery($sql, $params)
    {
        $sql_view = $sql;
        foreach ($params as $key => $value) {
            $sql_view = str_replace(':' . $key, "/***/'" . $value . "'/***/", $sql_view);
        }
        return $sql_view;
    }

    private function _setArgs($system, $args)
    {
        $this->system = $system;
        $this->args = $args;
        return $this;
    }

    private function debug($string, $source)
    {
        $message = 'Source: ' . $source . "\n" . $string;
        switch ($this->debug) {
            case 1:
                $this->logger->error($message);
                break;
            case 2:
                print "<pre>" . $message . "</pre>";
                break;
        }
    }

    public function getFilteredSignals($system, $args)
    {
        $this
            ->_setArgs($system, $args)
            ->_addFromTables()
            ->_addFilterRecently()
            ->_addFilterCall()
            ->_addFilterChannels()
            ->_addFilterFreq()
            ->_addFilterGsq()
            ->_addFilterNotes()
            ->_addFilterRange()
            ->_addFilterRegion()
            ->_addFilterStatesAndCountries()
            ->_addFilterStatus()
            ->_addFilterTypes();

        if (isset($args['show']) && $args['show'] === 'map') {
            $this->query['where'][] = '(s.lat != 0 OR s.lon !=0)';
        }
        if (isset($args['isAdmin']) && $args['isAdmin'] && $args['admin_mode'] === '1') {
            $this->_addFilterUnlogged();
        } elseif(isset($args['isAdmin']) && $args['isAdmin'] && $args['admin_mode'] === '2') {
            // No filter
        } else {
            $this
                ->_addFilterHeardIn()
                ->_addFilterListeners()
                ->_addFilterLoggedDate()
                ->_addFilterLoggedFirst()
                ->_addFilterLoggedLast()
                ->_addFilterSystem()
                ->_addFilterRwwFocus();
        }

        switch ($this->args['show'] ?? false) {
            case 'seeklist':
                $this
                    ->_addSelectColumnsAllSignalSeeklist()
                    ->_addSelectColumnPersonalise()
                    ->_addOrderForSeeklist();
                break;
            case 'map':
                $this
                    ->_addSelectColumnsAllSignal()
                    ->_addSelectColumnPersonalise()
                    ->_addSelectColumnRangeDeg()
                    ->_addSelectColumnRangeKm()
                    ->_addSelectColumnRangeMiles()

                    ->_addSelectPriotitizeActive()
                    ->_addSelectPriotitizeExactCall()
                    ->_addSelectPrioritizeNonEmpty()

                    ->_addOrderForListAndMap();
                break;
            default:
                $this
                    ->_addSelectColumnsAllSignal()
                    ->_addSelectColumnMorse()
                    ->_addSelectColumnNotes()
                    ->_addSelectColumnPersonalise()
                    ->_addSelectColumnsOffsets()
                    ->_addSelectColumnRangeDeg()
                    ->_addSelectColumnRangeKm()
                    ->_addSelectColumnRangeMiles()

                    ->_addSelectPriotitizeActive()
                    ->_addSelectPriotitizeExactCall()
                    ->_addSelectPrioritizeNonEmpty()

                    ->_addOrderForListAndMap();
                break;
        }
        switch ($this->args['show'] ?? false) {
            case 'csv':
            case 'seeklist':
                break;
            case 'list':
            case 'map':
            default:
                $this->_addLimit($args);
                break;
        }

        $sql = $this->_buildQuery();

        $stmt = $this->connection->prepare($sql);
        $this->debug(print_r($this->args, true), ($this->args['source'] ?? ''));
        $this->debug($this->_debugQuery($sql, $this->query['param']), ($this->args['source'] ?? ''));

        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getClePlanner($args)
    {
        $fields = [
            "s.id",
            "TRIM(s.khz) + 0 AS khz",
            "s.call AS `call`",
            "s.active",
            "s.decommissioned",
            "s.type",
            "s.ID",
            "s.QTH",
            "s.SP",
            "s.ITU",
            "s.region",
            "s.GSQ",
            "s.notes",
            "s.heard_in",
            "s.heard_in_html",
            "s.first_heard",
            "s.last_heard",
            "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active"
        ];
        $fields2 = [];
        $this->query['param'] = [];
        if ($args['date_1'] ?? false) {
            $this->query['param']['date_1'] = $args['date_1']->format('Y-m-d');
        }
        if ($args['date_2'] ?? false) {
            $this->query['param']['date_2'] = $args['date_2']->format('Y-m-d');
        }
        if ($args['khz_1'] ?? false) {
            $this->query['param']['khz_1'] = $args['khz_1'];
        }
        if ($args['khz_2'] ?? false) {
            $this->query['param']['khz_2'] = $args['khz_2'];
        }
        $status =   (($args['status'] ?? false) ?       $this->_getFilterForStatus($args['status']) : "");
        $channels = (($args['channels'] ?? false) ?     $this->_getFilterForChannels($args['channels']) : "");
        $types =    (($args['signalTypes'] ?? false) ?  $this->_getFilterForTypes($args['signalTypes']) : "");
        foreach (self::regions as $r) {
            $fields2[] =
                "    (SELECT COUNT(*) FROM logs l WHERE l.signalID = s.ID AND l.region = '$r'"
                . (($args['date_1'] ?? false) ? " AND l.date >= :date_1" : "")
                . (($args['date_2'] ?? false) ? " AND l.date <= :date_2" : "")
                . ") as `$r`";
        }
        $sql =
            "SELECT\n"
            . "    " . implode(",\n    ", $fields) . ",\n"
            . implode(",\n", $fields2) . "\n"
            . "FROM\n"
            . "    signals s\n"
            . "WHERE\n"
            . "    s.last_heard IS NOT NULL\n"
            . (($args['date_1'] ?? false) && !($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date >= :date_1) > 0\n"
                : ""
            )
            . (!($args['date_1'] ?? false) && ($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date <= :date_2) > 0\n"
                : ""
            )
            . (($args['date_1'] ?? false) && ($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date BETWEEN :date_1 AND :date_2) > 0\n"
                : ""
            )
            . (($args['khz_1'] ?? false) ?      "    AND s.khz >= :khz_1\n" : "")
            . (($args['khz_2'] ?? false) ?      "    AND s.khz <= :khz_2\n" : "")
            . ($status ?                        "    AND " . $status . "\n" : "")
            . ($channels ?                      "    AND " . $channels . "\n" : "")
            . ($types ?                         "    AND " . $types . "\n" : "")

            . "ORDER BY\n"
            . "    `_active`,\n"
            . "    `khz`,\n"
            . "    `call`\n";

        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $limit =    (int)$args['limit'];
            $offset =   (int)$args['page'] * (int)$args['limit'];
            $sql .=     "LIMIT\n    {$offset}, {$limit}";
        }


        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->query['param']);

//        return [$args, $sql, $this->query['param'], $this->_debugQuery($sql, $this->query['param'])];
        return $stmt->fetchAllAssociative();
    }

    public function getClePlannerCount($args)
    {
        $this->query['param'] = [];
        if ($args['date_1'] ?? false) {
            $this->query['param']['date_1'] = $args['date_1']->format('Y-m-d');
        }
        if ($args['date_2'] ?? false) {
            $this->query['param']['date_2'] = $args['date_2']->format('Y-m-d');
        }
        if ($args['khz_1'] ?? false) {
            $this->query['param']['khz_1'] = $args['khz_1'];
        }
        if ($args['khz_2'] ?? false) {
            $this->query['param']['khz_2'] = $args['khz_2'];
        }
        $status =   (($args['status'] ?? false) ?       $this->_getFilterForStatus($args['status']) : "");
        $channels = (($args['channels'] ?? false) ?     $this->_getFilterForChannels($args['channels']) : "");
        $types =    (($args['signalTypes'] ?? false) ?  $this->_getFilterForTypes($args['signalTypes']) : "");
        $sql =
            "SELECT\n"
            . "    COUNT(*)\n"
            . "FROM\n"
            . "    signals s\n"
            . "WHERE\n"
            . "    s.last_heard IS NOT NULL\n"
            . (($args['date_1'] ?? false) && !($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date >= :date_1) > 0\n"
                : ""
            )
            . (!($args['date_1'] ?? false) && ($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date <= :date_2) > 0\n"
                : ""
            )
            . (($args['date_1'] ?? false) && ($args['date_2'] ?? false) ?
                "    AND (SELECT COUNT(id) FROM logs l WHERE l.signalID = s.ID AND l.date BETWEEN :date_1 AND :date_2) > 0\n"
                : ""
            )
            . (($args['khz_1'] ?? false) ?      "    AND s.khz >= :khz_1\n" : "")
            . (($args['khz_2'] ?? false) ?      "    AND s.khz <= :khz_2\n" : "")
            . ($status ?                        "    AND " . $status . "\n" : "")
            . ($channels ?                      "    AND " . $channels . "\n" : "")
            . ($types ?                         "    AND " . $types . "\n" : "");

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->query['param']);
        return $stmt->fetchColumn();
    }

    public function getClePlannerStats($args)
    {
        $this->query['param'] = [];
        if ($args['date_1'] ?? false) {
            $this->query['param']['date_1'] = $args['date_1']->format('Y-m-d');
        }
        if ($args['date_2'] ?? false) {
            $this->query['param']['date_2'] = $args['date_2']->format('Y-m-d');
        }
        if ($args['khz_1'] ?? false) {
            $this->query['param']['khz_1'] = $args['khz_1'];
        }
        if ($args['khz_2'] ?? false) {
            $this->query['param']['khz_2'] = $args['khz_2'];
        }
        $status =   (($args['status'] ?? false) ?       $this->_getFilterForStatus($args['status']) : "");
        $channels = (($args['channels'] ?? false) ?     $this->_getFilterForChannels($args['channels']) : "");
        $types =    (($args['signalTypes'] ?? false) ?  $this->_getFilterForTypes($args['signalTypes']) : "");
        foreach (self::regions as $r) {
            $fields[] =
                "    (SELECT COUNT(DISTINCT SignalID) FROM logs l INNER JOIN signals s on l.signalID = s.ID WHERE l.region = '$r'"
                . (($args['date_1'] ?? false) ?     " AND l.date >= :date_1" : "")
                . (($args['date_2'] ?? false) ?     " AND l.date <= :date_2" : "")
                . (($args['khz_1'] ?? false) ?      " AND s.khz >= :khz_1" : "")
                . (($args['khz_2'] ?? false) ?      " AND s.khz <= :khz_2" : "")
                . ($status ?                        " AND " . $status : "")
                . ($channels ?                      " AND " . $channels : "")
                . ($types ?                         " AND " . $types : "")
                . ") as `$r`";
        }
        $sql =
            "SELECT\n"
            . "    " . implode(",\n    ", $fields) . "\n";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->query['param']);
        return $stmt->fetchAllAssociative();
    }

    /**
     * @param $system
     * @param $args
     * @return false|integer
     */
    public function getFilteredSignalsCount($system, $args)
    {
        $this
            ->_setArgs($system, $args)
            ->_addSelectColumnCountSignal()
            ->_addFromTables()
            ->_addFilterRecently()
            ->_addFilterCall()
            ->_addFilterChannels()
            ->_addFilterFreq()
            ->_addFilterGsq()
            ->_addFilterNotes()
            ->_addFilterRange()
            ->_addFilterRegion()
            ->_addFilterStatesAndCountries()
            ->_addFilterStatus()
            ->_addFilterTypes();

        if (isset($args['show']) && $args['show'] === 'map') {
            $this->query['where'][] = '(s.lat != 0 OR s.lon !=0)';
        }
        if ($args['isAdmin'] && $args['admin_mode'] === '1') {
            $this->_addFilterUnlogged();
        } elseif($args['isAdmin'] && $args['admin_mode'] === '2') {
            // No filter
        } else {
            $this
                ->_addFilterHeardIn()
                ->_addFilterListeners()
                ->_addFilterLoggedDate()
                ->_addFilterLoggedFirst()
                ->_addFilterLoggedLast()
                ->_addFilterSystem()
                ->_addFilterRwwFocus();
        }
        $sql = $this->_buildQuery();

        $this->debug($this->_debugQuery($sql, $this->query['param']), $this->args['source']);

        $stmt = $this->connection->prepare($sql);
        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * @param string $mode
     * @return false|array
     */
    public function getColumns($mode = '')
    {
        switch ($mode) {
            case 'listeners':
                return $this->signalListenersColumns;
            case 'logs':
                return $this->signalLogsColumns;
            case 'signals':
                return $this->signalsColumns;
        }
        return false;
    }

    public function getDx($signalID, $qthLat, $qthLon)
    {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.lat, s.lon')
            ->andWhere('s.id = :signalID')
            ->setParameter(':signalID', $signalID);

        $record = $qb->getQuery()->execute();
        return Rxx::getDx($qthLat, $qthLon, $record[0]['lat'], $record[0]['lon']);
    }

    public function getSignalType($signalID)
    {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s.type')
            ->andWhere('s.id = :signalID')
            ->setParameter(':signalID', $signalID);

        $record = $qb->getQuery()->execute();
        return $record[0]['type'];
    }

    public function getSignals(array $args = [], array $columns = [])
    {
        $fields =
            's.id,'
            .'trim(s.khz)+0 AS khz,'
            .'s.active,'
            .'s.call,'
            .'s.qth,'
            .'s.sp,'
            .'s.itu,'
            .'s.region,'
            .'s.gsq,'
            .'s.type,'
            .'(CASE WHEN s.lsb = 0 THEN \'\' ELSE CONCAT(s.lsbApprox, s.lsb) END) AS lsb,'
            .'(CASE WHEN s.usb = 0 THEN \'\' ELSE CONCAT(s.usbApprox, s.usb) END) AS usb,'
            .'trim(s.sec)+0 AS sec,'
            .'(CASE WHEN trim(s.sec)+0 = 0 THEN \'\' ELSE trim(s.sec)+0 END) AS secF,'
            .'s.format,'
            .'(CASE WHEN s.pwr = 0 THEN \'\' ELSE s.pwr END) AS pwr,'
            .'s.lat,'
            .'s.lon,'
            .'s.notes,'
            .'s.heardIn,'
            .'s.lastHeard,'
            .'l.dxKm,'
            .'l.dxMiles,'
            .'l.dxDeg,'
            .'COUNT(l.signalId) AS logs,'
            .'MAX(l.daytime) AS daytime,'
            .'MIN(l.date) AS earliest,'
            .'MAX(l.date) AS latest';

//        print "<pre>ARGS: ".print_r($args, true)."</pre>";
        $qb = $this
            ->createQueryBuilder('li')
            ->select($fields)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.listenerId = li.id')

            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalId = s.id');

        if (isset($args['listenerID']) && $args['listenerID'] !== '') {
            $qb
                ->andWhere('li.id = :listenerID')
                ->setParameter('listenerID', $args['listenerID']);
        }

        if (isset($args['logsessionID']) && $args['logsessionID'] !== '') {
            $qb
                ->andWhere('l.logSessionId = :logsessionID')
                ->setParameter('logsessionID', $args['logsessionID']);
        }

        if (isset($args['type']) && $args['type'] !== '') {
            $qb
                ->andWhere('s.type in(:type)')
                ->setParameter('type', $args['type']);
        }

        if (isset($args['latlon']) && $args['latlon'] === true) {
            $qb
                ->andWhere('(s.lat != 0 AND s.lon != 0)');
        }

        if (isset($args['active']) && $args['active'] !== '') {
            $qb
                ->andWhere('s.active = :active')
                ->setParameter('active', $args['active']);
        }

        $qb
            ->groupBy('s.id, l.dxKm, l.dxMiles');

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $columns[$args['sort']]['sort']) {
            $idx = $columns[$args['sort']];
//            print "<pre>COLUMN: ".print_r($idx, true)."</pre>";
            $qb ->addOrderBy(
                ($idx['sort']),
                (isset($args['order']) && $args['order'] === 'd' ? 'DESC' : 'ASC')
            );
            if (isset($args['order']) && isset($idx['sort_2']) && isset($idx['order_2'])) {
                $qb ->addOrderBy(
                    ($idx['sort_2']),
                    ($args['order'] === 'd' ?
                        ($idx['order_2'] === 'a' ? 'DESC' : 'ASC')
                        :
                        ($idx['order_2'] === 'd' ? 'DESC' : 'ASC')
                    )
                );
            }
        }

        $result = $qb->getQuery()->execute();
        foreach ($result as &$row) {
            $row['qth'] = str_replace("\"", "\\\"", html_entity_decode($row['qth']));
            $row['notes'] = str_replace("\"", "\\\"", html_entity_decode($row['notes']));
        }
//        print "<pre>QUERY: ".print_r($qb->getQuery()->getSQL(), true)."</pre>";
        return $result;
    }

    public static function getSeeklistTabulatedData($signals, $paper)
    {
        $col = 0;
        $col_length = 0;
        $old_place = false;

        $data = [];
        foreach ($signals as $s) {
            $place = $s['itu'] . '_' . $s['sp'];
            if ($place !== $old_place) {
                $col_length += 2;
                $old_place = $place;
            }
            if ($col_length > $paper['rows']) {
                $col_length = 0;
                $col++;
            }
            if (!isset($data[$col])) {
                $data[$col] = [];
            }
            $data[$col][] = $s;
            $col_length ++;
        }
        return $data;
    }

    public static function getSeeklistStats($signals)
    {
        $itu_sp = [ 'all' => ['total' => 0, 'heard' => 0 ] ];

        foreach ($signals as $s) {
            $place = $s['itu'] . '_' . $s['sp'];
            if (!isset($itu_sp[$place])) {
                $itu_sp[$place] = [ 'total' => 0, 'heard' => 0 ];
            }
            $itu_sp[$place]['total']++;
            $itu_sp['all']['total']++;
            $itu_sp[$place]['heard'] += $s['personalise'] ? 1 : 0;
            $itu_sp['all']['heard'] += $s['personalise'] ? 1 : 0;
        }

        return $itu_sp;
    }

    public function getListenersForSignal($signalID, array $args)
    {
        $fields = <<< EOD
            li.id, li.primaryQth, li.gsq, li.itu, li.name, li.qth, li.sp,
            l.dxKm, l.dxMiles,
            COUNT(l.id) AS countLogs,
            MAX(l.daytime) AS daytime
EOD;

        $qb = $this
            ->createQueryBuilder('s')
            ->select($fields)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.signalId = s.id')
            ->innerJoin('\App\Entity\Listener', 'li')
            ->andWhere('li.id = l.listenerId')
            ->andWhere('s.id = :signalID')
            ->setParameter(':signalID', $signalID)
            ->addGroupBy('li.id, l.dxKm, l.dxMiles');

        $this->addSimpleLimit($qb, $args);
        $this->addSimpleSort($qb, $args, $this->signalListenersColumns);

        return $qb->getQuery()->execute();
    }

    public function getLogsForSignal($signalID, array $args)
    {
        $fields = <<< EOD
            l.id AS log_id,
            l.operatorId,
            (CASE WHEN op.name IS NULL THEN '' ELSE op.name END) as operator,
            l.date,
            l.dxKm,
            l.dxMiles,
            l.format,
            l.sec,
            l.time,
            li.id,
            li.name,
            li.primaryQth,
            li.qth,
            li.gsq,
            li.sp,
            li.itu,
            CONCAT(COALESCE(l.lsbApprox, ''), l.lsb) AS lsb,
            CONCAT(COALESCE(l.usbApprox, ''), l.usb) AS usb,
            MAX(l.daytime) AS daytime
EOD;

        $qb = $this
            ->createQueryBuilder('s')
            ->select($fields)
            ->innerJoin('\App\Entity\Log', 'l', 'WITH', 'l.signalId = s.id')
            ->innerJoin('\App\Entity\Listener', 'li', 'WITH', 'li.id = l.listenerId')
            ->leftJoin('\App\Entity\Listener', 'op', 'WITH', 'l.operatorId = op.id')
            ->andWhere('s.id = :signalID')
            ->setParameter(':signalID', $signalID)
            ->addGroupBy('l.id');

        $this->addSimpleLimit($qb, $args);
        $this->addSimpleSort($qb, $args, $this->signalLogsColumns);

        return $qb->getQuery()->execute();
    }

    private function addSimpleLimit($qb, $args)
    {
        if (isset($args['limit']) && (-1 !== (int)$args['limit']) && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }
    }

    private function addSimpleSort($qb, $args, $refColumns)
    {
        $idx = $refColumns[$args['sort']];
        if (isset($idx['sort'])) {
            $qb->addSelect('CASE WHEN ' . $idx['sort'] . ' IS NULL THEN 1 ELSE 0 END AS N1');
            $qb->addOrderBy('N1', 'ASC');
            $qb->addOrderBy($idx['sort'], ('d' === $args['order'] ? 'DESC' : 'ASC'));
            if (isset($idx['sort_2'])) {
                $qb->addSelect('CASE WHEN ' . $idx['sort_2'] . ' IS NULL THEN 1 ELSE 0 END AS N2');
                $qb->addOrderBy('N2', 'ASC');
                $qb->addOrderBy($idx['sort_2'], ('d' === $args['order'] ? 'DESC' : 'ASC'));
            }
        }
    }

    public function getTabs(Signal $signal): array
    {
        $out = [];
        if (!$signal->getId()) {
            return $out;
        }
        $logs =         $signal->getLogs();
        $listeners =    $signal->getListeners();
        $knownQth =     ($signal->getLat() || $signal->getLon());
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            $label = $data[1];
            switch ($route) {
                case "signal_logs":
                    if ($logs) {
                        $label = sprintf($label, $logs);
                        $out[] = [$route, $label, false];
                    }
                    break;
                case "signal_listeners":
                    if ($listeners) {
                        $label = sprintf($label, $listeners);
                        $out[] = [$route, $label, false];
                    }
                    break;
                case 'signal_weather':
                    if ($knownQth) {
                        $out[] = $data;
                    }
                    break;
                case 'signal_map':
                    if ($knownQth) {
                        $out[] = $data;
                    }
                    break;
                case 'signal_rx_map_eu':
                    if ($signal->getHeardInEu()) {
                        $out[] = $data;
                    }
                    break;
                case 'signal_rx_map_na':
                    if ($signal->getHeardInNa() || $signal->getHeardInCa()) {
                        $out[] = $data;
                    }
                    break;
                default:
                    $out[] = [$route, $label, false];
                    break;
            }
        }
        return $out;
    }

    public function array_group_by($key, $data) {
        $result = [];
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[''][] = $val;
            }
        }
        return $result;
    }

    public function getLatestLogDateAndTime($signalID)
    {
        $sql = <<<EOD
            SELECT
	            date,
                time
            FROM
	            logs
            WHERE
                signalID = :signalID
            ORDER BY
                date DESC,
                time DESC
            LIMIT 1
EOD;
        $params = [ ':signalID' => $signalID ];
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results[0] ?? false;
    }

    public function isLatestLogDateAndTime($signalID, $YYYYMMDD, $hhmm)
    {
        $latestLog = $this->getLatestLogDateAndTime($signalID);
        if (!$latestLog) {
            return true;
        }
        if ($YYYYMMDD > $latestLog['date']) {
            return true;
        }
        if ($YYYYMMDD === $latestLog['date'] && $hhmm && $hhmm > $latestLog['time']) {
            return true;
        }
        return false;
    }

    private function getLogsLatestSpec($signalId = false)
    {
        // This takes WAY longer for mysql 5.5 so don't use for all signals on that server
        $sql = <<<EOD
SELECT
    signalID,
    (SELECT LSB FROM logs l WHERE l.signalID = logs.signalID AND (l.LSB IS NOT NULL AND l.LSB != 0) AND (l.LSB_approx IS NULL OR l.LSB_approx = '') ORDER BY l.date DESC, l.time DESC LIMIT 1) as LSB,
    (SELECT USB FROM logs l WHERE l.signalID = logs.signalID AND (l.USB IS NOT NULL AND l.USB != 0) AND (l.USB_approx IS NULL OR l.USB_approx = '') ORDER BY l.date DESC, l.time DESC LIMIT 1) as USB,
    (SELECT sec FROM logs l WHERE l.signalID = logs.signalID AND (l.sec IS NOT NULL AND l.sec != '') ORDER BY l.date DESC LIMIT 1) as sec
FROM
    logs
GROUP BY
    signalID
EOD;
        if ($signalId) {
            $sql = str_replace('GROUP BY', "WHERE\n    logs.signalID = $signalId\nGROUP BY", $sql);
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($results as $r) {
            $out[$r['signalID']] = [
                'LSB' =>    $r['LSB'],
                'USB' =>    $r['USB'],
                'sec' =>    $r['sec']
            ];
        }
        return $out;
    }

    private function getLogsHeardIn($signalId = false)
    {
        $WHERE = ($signalId ? "WHERE\n    signalID = $signalId" : '');
        $sql = <<<EOD
            SELECT
                signalID,
                heard_in,
                MAX(daytime) AS daytime,
                region
            FROM
                logs
            $WHERE
            GROUP BY
                heard_in,
                region,
                signalID
            ORDER BY
                signalID,
                (region='na' OR region='ca' OR (region='oc' AND heard_in='HI')),
                region,
                heard_in
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->array_group_by('signalID', $results);
    }

    public function getLogsStats($signalId = false)
    {
        $WHERE = ($signalId ? "WHERE\n    signalID = $signalId" : '');
        $sql = <<<EOD
            SELECT
                signalID,
                COUNT(*) AS count_logs,
                COUNT(DISTINCT listenerID) as count_listeners,
                MIN(`date`) as first_heard,
                MAX(`date`) as last_heard
            FROM
                logs
            $WHERE
            GROUP BY
                signalID
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($results as $r) {
            $out[$r['signalID']] = [
                'first_heard' =>    $r['first_heard'],
                'last_heard' =>     $r['last_heard'],
                'logs' =>           $r['count_logs'],
                'listeners' =>      $r['count_listeners']
            ];
        }
        return $out;
    }

    public function getAll()
    {
        $sql = <<< EOD
            SELECT
                REPLACE(
                    CONCAT_WS(
                        '|',
                        id,
                        `call`,
                        khz,
                        type,
                        gsq,
                        active,
                        qth,
                        sp,
                        itu
                    ),
                    '"',
                    '&quot;'
                ) as s
            FROM            
                `signals`
            ORDER BY
                khz,
                `call`;
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getSignalCandidates($call, $frequency, $listener)
    {
        if (!is_numeric($frequency)) {
            return [];
        }
        $lat = $listener->getLat();
        $lon = $listener->getLon();
        $swing = ($frequency > 1740 ? LogRepository::SWING_HF : LogRepository::SWING_LF);

        $sql = <<< EOD
            SELECT
                *
            FROM
                `signals`
            WHERE
                `call` = :call AND
                `khz` >= :min AND
                `khz` <= :max
EOD;
        $params = [
            ':call' =>  htmlentities($call),
            ':min' =>   $frequency - $swing,
            ':max' =>   $frequency + $swing
        ];
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$r) {
            $dx = Rxx::getDx($lat, $lon, $r['lat'], $r['lon']);
            $r['dx_miles'] =    $dx ? $dx['miles'] : '?';
            $r['dx_km'] =       $dx ? $dx['km'] : '?';
            $r['dx_deg'] =      $dx ? $dx['deg'] : '?';
        }
        usort($results, function($a, $b) {
            if($a['dx_km']===$b['dx_km']){
                return 0;
            };
            return ($a['dx_km'] > $b['dx_km'] ? 1 : -1);
        });
        return $results;
    }

    public function updateSignalLatLonFromGSQ($signalId = false)
    {
        $affected = 0;
        $errors = [];
        $WHERE = ($signalId ? "WHERE\n    signalID = $signalId" : '');
        $sql = <<< EOD
            SELECT
                `ID`,
                `call`,
                `GSQ`,
                `khz`,
                `lat`,
                `lon`
            FROM
                `signals`
            $WHERE
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $r) {
            $latlon = Rxx::convertGsqToDegrees($r['GSQ']);
            if (!$latlon) {
                $errors[] = "{$r['call']}-{$r['khz']} has invalid GSQ {$r['GSQ']}";
                continue;
            }
            if ((round($r['lat'], 3) === round($latlon['lat'], 3)) &&
                (round($r['lon'], 3) === round($latlon['lon'], 3))
            ) {
                continue;
            }
            $sql = <<< EOD
                UPDATE
                    `signals`
                SET
                    `GSQ` = '{$latlon['GSQ']}',
                    `lat` = {$latlon['lat']},
                    `lon` = {$latlon['lon']}
                WHERE
                    `ID` = {$r['ID']}
EOD;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $affected += $stmt->rowCount();
        }
        return $affected;
    }

    public function updateSignalStats($signalId = false, $updateSpecs = false, $clearUnheard = false)
    {
        $affected = 0;
        $all_regions =      ['af', 'an', 'as', 'ca', 'eu', 'iw', 'na', 'oc', 'sa'];
        $logsHeardIn =      $this->getLogsHeardIn($signalId);
        $logsStats =        $this->getLogsStats($signalId);
        if ($updateSpecs) {
            $logsLatestSpec = $this->getLogsLatestSpec($signalId);
        }
        if ($clearUnheard) {
            $sql = <<< EOD
UPDATE
    `signals`
SET
    `heard_in` = '',
    `heard_in_html` = '',
    `last_heard` = NULL,
    `logs` = 0,
    `listeners` = 0,
    `heard_in_af` = 0,
    `heard_in_an` = 0,
    `heard_in_as` = 0,
    `heard_in_ca` = 0,
    `heard_in_eu` = 0,
    `heard_in_iw` = 0,
    `heard_in_na` = 0,
    `heard_in_oc` = 0,
    `heard_in_sa` = 0
WHERE
    ID NOT IN (SELECT distinct signalID from logs)
EOD;

            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $affected += $stmt->rowCount();
        }

        $data =         [];
        foreach ($logsHeardIn as $signalID => $result) {
            $heardIn =      [];
            $regions =      [];
            $old_link =     false;
            foreach ($result as $row) {
                $region = $row['region'];
                $regions[$region] = $region;
                switch ($region) {
                    case "ca":
                    case "na":
                        $link = "<a data-signal-map-na='%s'>";
                        break;
                    case "oc":
                        $link = ('HI' === $row["heard_in"] ? "<a data-signal-map-na='%s'>" : false);
                        break;
                    case "eu":
                        $link = "<a data-signal-map-eu='%s'>";
                        break;
                    default:
                        $link = false;
                }
                $heardIn[] =
                    ($old_link && ($link !== $old_link) ? '</a> ' : ' ')
                    . (($link ?? false) && ($link !== $old_link) ? sprintf($link, $row['signalID']) : '')
                    . ' '. ($row["daytime"] ? sprintf("<b>%s</b>", $row["heard_in"]) : $row["heard_in"]);
                $old_link = $link;
            }
            if ($link !== false) {
                $heardIn[] = "</a>";
            }
            $entry = [
                'ID' =>             $row['signalID'],
                'heard_in' =>       trim(strip_tags(implode('', $heardIn))),
                'heard_in_html' =>  preg_replace('/\s+/', ' ', trim(implode('', $heardIn)))
            ];
            foreach($all_regions as $r) {
                $entry['heard_in_' . $r] = (isset($regions[$r]) ? '1' : '0');
            }
            $data[$row['signalID']] = $entry;
        }
        foreach ($logsStats as $signalID => $stats) {
            $data[$signalID] = array_merge($data[$signalID], $stats);
        }
        if ($updateSpecs) {
            foreach ($logsLatestSpec as $signalID => $spec) {
                $data[$signalID] = array_merge($data[$signalID], $spec);
            }
        }
        $sql = "SELECT * FROM `signals`";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $signals = $this->array_group_by('ID', $results);

        $fields = [
            'first_heard', 'heard_in', 'heard_in_html', 'last_heard', 'logs', 'listeners',
            'heard_in_af', 'heard_in_an', 'heard_in_as', 'heard_in_ca', 'heard_in_eu',
            'heard_in_iw', 'heard_in_na', 'heard_in_oc', 'heard_in_sa'
        ];
        foreach ($data as $signalID => $new) {
            $old = $signals[$signalID][0];
            $update = false;
            foreach ($fields as $f) {
                if ($old[$f] !== $new[$f]) {
                    $update = true;
                    break;
                }
            }
            if ($updateSpecs) {
                if ($new['LSB'] !== null && $new['LSB'] !== $old['LSB']) {
                    $update = true;
                }
                if ($new['USB'] !== null && $new['USB'] !== $old['USB']) {
                    $update = true;
                }
                if ($new['sec'] !== null && $new['sec'] !== $old['sec']) {
                    $update = true;
                }
            }
            if (!$update) {
                continue;
            }
            $sql = "
UPDATE
    signals
SET "
    . ($updateSpecs && $new['LSB'] !== null ?
          "\n    `LSB_approx` =      '',"
        . "\n    `LSB` =             '" . addslashes($new['LSB']) . "',"
    : '')
    . ($updateSpecs && $new['USB'] !== null ?
          "\n    `USB_approx` =      '',"
        . "\n    `USB` =             '" . addslashes($new['USB']) . "',"
    : '')
    . ($updateSpecs && $new['sec'] !== null ?
        "\n    `sec` =             '" . addslashes($new['sec']) . "',"
    : '') . "
    `first_heard` =     '" . addslashes($new['first_heard']) . "',
    `heard_in` =        '" . addslashes($new['heard_in']) . "',
    `heard_in_html` =   '" . addslashes($new['heard_in_html']) . "',
    `heard_in_af` =     '" . $new['heard_in_af'] . "',
    `heard_in_an` =     '" . $new['heard_in_an'] . "',
    `heard_in_as` =     '" . $new['heard_in_as'] . "',
    `heard_in_ca` =     '" . $new['heard_in_ca'] . "',
    `heard_in_eu` =     '" . $new['heard_in_eu'] . "',
    `heard_in_iw` =     '" . $new['heard_in_iw'] . "',
    `heard_in_na` =     '" . $new['heard_in_na'] . "',
    `heard_in_oc` =     '" . $new['heard_in_oc'] . "',
    `heard_in_sa` =     '" . $new['heard_in_sa'] . "',
    `last_heard` =      '" . addslashes($new['last_heard']) . "',
    `logs` =            '" . addslashes($new['logs']) . "',
    `listeners` =       '" . addslashes($new['listeners']) . "'
WHERE
    ID =                $signalID";
//            print "<pre>$sql</pre>"; die;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $affected += $stmt->rowCount();
        }

        return $affected;
    }

    public function getDgpsForLookup()
    {
        $typeDgps = 1;
        $sql = <<< EOD
            SELECT
                `signals`.*
            FROM
                `signals`
            INNER JOIN `itu` ON
                `signals`.`ITU` = `itu`.`ITU`
            WHERE
                `signals`.`type` = $typeDgps
EOD;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out =    '';

        foreach ($results as $row) {
            if (preg_match("/Ref ID: ([0-9]+)\/*([0-9]+)*; *([0-9]+) *bps/i", $row['notes'], $ID_arr)) {
                $out.=
                    "DGPS.a('"
                    . (count($ID_arr)>1 ? $ID_arr[1] : "") . "', '"
                    . (count($ID_arr)>2 ? $ID_arr[2] : "") . "', '"
                    . $row['call'] . "', '"
                    . (float)$row['khz'] . "', '"
                    . (count($ID_arr)>2 ? $ID_arr[3] : '') . "', \""
                    . $row['QTH'] . "\", '"
                    . $row['SP'] . "', '"
                    . $row['ITU'] . "', "
                    . $row['active'] .");\n";
            }
        }
        return $out;
    }

}
