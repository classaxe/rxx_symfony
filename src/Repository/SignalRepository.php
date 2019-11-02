<?php

namespace App\Repository;

use App\Columns\Signals as SignalsColumns;
use App\Entity\Signal;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SignalRepository extends ServiceEntityRepository
{
    private $args;
    private $connection;
    private $debug = false;
    private $query = [
        'from' =>   [],
        'having' => [],
        'limit' =>  [],
        'order' =>  [],
        'param' =>  [],
        'select' => [],
        'where' =>  [],
    ];
    private $signalsColumns;
    private $system;

    public function __construct(
        RegistryInterface $registry,
        Connection $connection,
        SignalsColumns $signalsColumns

    ) {
        parent::__construct($registry, Signal::class);
        $this->connection = $connection;
        $this->signalsColumns = $signalsColumns->getColumns();
    }

    private function addFilterActive()
    {
        if (isset($this->args['active']) && in_array($this->args['active'], ['1', '2'])) {
            $this->query['where'][] ='(s.active = :active)';
            $this->query['param']['active'] = ($this->args['active'] === '2' ? 0 : 1);
        }
        return $this;
    }

    private function addFilterCall()
    {
        if (isset($this->args['call']) && $this->args['call'] !== '') {
            $this->query['where'][] ='(s.call LIKE :like_call)';
            $this->query['param']['like_call'] = '%' . $this->args['call'] . '%';
        }
        return $this;
    }

    private function addFilterChannels()
    {
        switch ($this->args['channels']) {
            case 1:
                $this->query['where'][] ='MOD(s.khz * 1000, 1000) = 0';
                break;
            case 2:
                $this->query['where'][] ='MOD(s.khz * 1000, 1000) != 0';
                break;
        }
        return $this;
    }

    private function addFilterFreq()
    {
        $khz_1 = (float)$this->args['khz_1'] ? (float)$this->args['khz_1'] : 0;
        $khz_2 = (float)$this->args['khz_2'] ? (float)$this->args['khz_2'] : 1000000;

        if ($khz_1 !== 0 || $khz_2 !== 1000000) {
            $this->query['where'][] ='(s.khz BETWEEN :khz1 AND :khz2)';
            $this->query['param']['khz1'] = $khz_1;
            $this->query['param']['khz2'] = $khz_2;
        }
        return $this;
    }

    private function addFilterGsq()
    {
        if (isset($this->args['gsq']) && $this->args['gsq'] !== '') {
            $gsq = explode(" ", str_replace('*', '%', $this->args['gsq']));
            $in = $this->buildInParamsList('gsq', $gsq, '', '%');
            $this->query['where'][] = "s.gsq LIKE " . implode($in, " OR s.gsq LIKE ");
        }
        return $this;
    }

    private function addFilterHeardIn()
    {
        if (isset($this->args['heard_in'])) {
            $heard_in_arr = explode(' ', $this->args['heard_in']);
            if (isset($this->args['heard_in_mod']) && $this->args['heard_in_mod'] === 'any') {
                $in = $this->buildInParamsList('heard_in', $heard_in_arr);
                $this->query['where'][] =
                    "l.heard_in IN (" . implode(', ', $in) . ")";
            } else {
                $in = $this->buildInParamsList('heard_in', $heard_in_arr, '%', '%');
                $this->query['where'][] =
                    "s.heard_in LIKE " . implode(' AND s.heard_in LIKE ', $in);
            }
        }
        return $this;
    }

    private function addFilterListeners()
    {
        // Special case: selected 'all' listeners, and 'not heard by'
        if (isset($this->args['listener_invert']) && !isset($this->args['listener'])) {
            $this->query['where'][] = "0=1";
            return $this;
        }
        if (isset($this->args['listener'])) {
            $in = $this->buildInParamsList('heard_by', $this->args['listener']);
            if (isset($this->args['listener_invert'])) {
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
            } else {
                $this->query['where'][] =
                    "l.listenerId IN (" . implode(',', $in) . ")";
            }
        }
        return $this;
    }

    private function addFilterLoggedDate()
    {
        if (isset($this->args['logged_date_1']) || isset($this->args['logged_date_2'])) {
            $this->query['where'][] = "l.date between :logged_date_1 AND :logged_date_2";
            $this->query['param']['logged_date_1'] = (isset($this->args['logged_date_1']) ?
                $this->args['logged_date_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_date_2'] = (isset($this->args['logged_date_2']) ?
                $this->args['logged_date_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function addFilterLoggedFirst()
    {
        if (isset($this->args['logged_first_1']) || isset($this->args['logged_first_2'])) {
            $this->query['where'][] = "s.first_heard between :logged_first_1 AND :logged_first_2";
            $this->query['param']['logged_first_1'] = (isset($this->args['logged_first_1']) ?
                $this->args['logged_first_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_first_2'] = (isset($this->args['logged_first_2']) ?
                $this->args['logged_first_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function addFilterLoggedLast()
    {
        if (isset($this->args['logged_last_1']) || isset($this->args['logged_last_2'])) {
            $this->query['where'][] = "s.last_heard between :logged_last_1 AND :logged_last_2";
            $this->query['param']['logged_last_1'] = (isset($this->args['logged_last_1']) ?
                $this->args['logged_last_1']->format('Y-m-d') : "1900-01-01"
            );
            $this->query['param']['logged_last_2'] = (isset($this->args['logged_last_2']) ?
                $this->args['logged_last_2']->format('Y-m-d') : "2100-01-01"
            );
        }
        return $this;
    }

    private function addFilterRange()
    {
        if (isset($this->args['range_gsq']) &&
            $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $min = (float)$this->args['range_min'] ? (float)$this->args['range_min'] : 0;
            $max = (float)$this->args['range_max'] ? (float)$this->args['range_max'] : 1000000;
            $mult = $this->args['range_units'] ==='km' ? RXX::DEG_KM_MULTIPLIER : RXX::DEG_MI_MULTIPLIER;

            if ($min !== 0 || $max !== 1000000) {
                if (isset($this->args['range_gsq']) &&
                    $this->args['range_gsq'] !== '' &&
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

    private function addFilterRegion()
    {
        if (isset($this->args['region']) && $this->args['region'] !== '') {
            $this->query['where'][] = '(s.region = :region)';
            $this->query['param']['region'] = $this->args['region'];
        }
        return $this;
    }

    private function addFilterStatesAndCountries()
    {
        $clauses = [];
        if (isset($this->args['countries']) && $this->args['countries'] !== '') {
            $countries = explode(" ", str_replace('*', '%', $this->args['countries']));
            $in = $this->buildInParamsList('countries', $countries);
            $clauses[] = "(s.itu LIKE " . implode($in, " OR s.itu LIKE ") . ")";
        }
        if (isset($this->args['states']) && $this->args['states'] !== '') {
            $states = explode(" ", str_replace('*', '%', $this->args['states']));
            $in = $this->buildInParamsList('states', $states);
            $clauses[] = "(s.sp LIKE " . implode($in, " OR s.sp LIKE ") . ")";
        }
        switch (count($clauses)) {
            case 0:
                break;
            case 1:
                $this->query['where'][] = $clauses[0];
                break;
            case 2:
                $this->query['where'][] = $clauses[0] . " " . $this->args['sp_itu_clause'] . " " . $clauses[1];
                break;
        }
        return $this;
    }

    private function addFilterSystem()
    {
        switch ($this->system) {
            case "reu":
                $this->query['where'][] ='s.heard_in_eu = 1';
                break;
            case "rna":
                $this->query['where'][] ='s.heard_in_na = 1 OR s.heard_in_ca = 1';
                break;
            case "rww":
                $this->query['where'][] = "s.logs > 0";
                break;
        }
        return $this;
    }

    private function addFilterTypes()
    {
        $in = $this->buildInParamsList('types', $this->args['signalTypes'], '', '');
        $this->query['where'][] = "s.type IN(" . implode(',', $in).")";
        return $this;
    }

    private function addFromTables()
    {
        if (
            isset($this->args['listener']) ||
            isset($this->args['heard_in']) ||
            isset($this->args['logged_date_1']) ||
            isset($this->args['logged_date_2']) ||
            isset($this->args['first_logged_1']) ||
            isset($this->args['first_logged_2']) ||
            isset($this->args['last_logged_1']) ||
            isset($this->args['last_logged_2'])
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

    private function addLimit($args)
    {
        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $limit =    $args['limit'];
            $offset =   (int)$args['page'] * (int)$args['limit'];
            $this->query['limit'][] = "{$offset}, {$limit}";
        }
        return $this;
    }

    private function addOrder($field, $dir)
    {
        $this->query['order'][] = "{$field} {$dir}";
        return $this;
    }

    private function addOrderForSeeklist()
    {
        $this
            ->addOrder('s.itu','ASC')
            ->addOrder('s.sp', 'ASC')
            ->addOrder('s.khz', 'ASC')
            ->addOrder('s.call', 'ASC');
        return $this;
    }

    private function addOrderPrioritizeActive()
    {
        if (!isset($this->args['active']) || $this->args['active'] === '') {
            $this->addOrder('_active','ASC');
        }
        return $this;
    }

    private function addOrderPrioritizeExactCall()
    {
        if ($this->args['call']) {
            $this->addOrder('_call','DESC');
        }
        return $this;
    }

    private function addOrderPrioritizeSelected()
    {
        if ($this->signalsColumns[$this->args['sort']]['sort']) {
            $this->addOrder(
                '_empty',
                'ASC'
            );
            if (in_array($this->args['sort'], ['LSB', 'USB']) &&
                isset($this->args['offsets']) &&
                $this->args['offsets'] === '1'
            ) {
                $this->addOrder(
                    ('s.khz ' . ($this->args['sort'] === 'USB' ? '+' : '-') . ' (s.' . $this->args['sort'].'/1000)'),
                    ($this->args['order'] === 'd' ? 'DESC' : 'ASC')
                );
            } else {
                $this->addOrder(
                    ($this->signalsColumns[$this->args['sort']]['sort']),
                    ($this->args['order'] === 'd' ? 'DESC' : 'ASC')
                );
            }
        }
        return $this;
    }

    private function addSelectColumnPersonalise()
    {
        if (isset($this->args['personalise']) && $this->args['personalise'] !== '') {
            $this->query['select'][] =
                'IF (s.ID IN(SELECT l.signalID from logs l where l.listenerID = :personalise), 1, 0) AS personalise';
            $this->query['param']['personalise'] = $this->args['personalise'];
        } else {
            $this->query['select'][] = "0 as personalise";
        }
        return $this;
    }

    private function addSelectColumnRangeDeg()
    {
        if (isset($this->args['range_gsq']) && $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $this->query['select'][] =
                "CAST(\n"
                . "      COALESCE(\n"
                . "         ROUND(\n"
                . "          (\n"
                . "            DEGREES(\n"
                . "              ATAN2(\n"
                . "                (SIN(RADIANS(s.lon) - RADIANS(:lon)) * COS(RADIANS(s.lat))),\n"
                . "                ((COS(RADIANS(:lat)) * SIN(RADIANS(s.lat))) - SIN(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(s.lon) - RADIANS(:lon)))\n"
                . "              )\n"
                . "            ) + 360\n"
                . "          ) MOD 360\n"
                . "        ),\n"
                . "        ''\n"
                . "       ) AS UNSIGNED\n"
                . "    ) AS range_deg";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_deg";
        }
        return $this;
    }

    private function addSelectColumnRangeKm() {
        if (isset($this->args['range_gsq']) &&
            $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $this->query['select'][] =
                "CAST(\n"
                . "      COALESCE(\n"
                . "        ROUND(\n"
                . "          DEGREES(\n"
                . "            ACOS(\n"
                . "              (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                . "              (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                . "            )\n"
                . "          ) * ".RXX::DEG_KM_MULTIPLIER.",\n"
                . "          2\n"
                . "        ), ''\n"
                . "      ) AS UNSIGNED\n"
                . "    ) AS range_km";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_km";
        }
        return $this;
    }

    private function addSelectColumnRangeMiles()
    {
        if (isset($this->args['range_gsq']) &&
            $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $this->query['select'][] =
                "CAST(\n"
                . "      COALESCE(\n"
                . "        ROUND(\n"
                . "          DEGREES(\n"
                . "            ACOS(\n"
                . "              (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                . "              (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                . "            )\n"
                . "          ) * ".RXX::DEG_MI_MULTIPLIER.",\n"
                . "          2\n"
                . "        ), ''\n"
                . "      ) AS UNSIGNED\n"
                . "    ) AS range_mi";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS range_mi";
        }
        return $this;
    }

    private function addSelectColumnsOffsets()
    {
        if (isset($this->args['offsets']) && $this->args['offsets'] === '1') {
            $this->query['select'][] = "ROUND(s.khz - (s.LSB/1000), 3) as LSB";
            $this->query['select'][] = "ROUND(s.khz + (s.USB/1000), 3) as USB";
        } else {
            $this->query['select'][] = "s.LSB as LSB";
            $this->query['select'][] = "s.USB as USB";
        }
        return $this;
    }

    private function addSelectPriotitizeActive()
    {
        if (!isset($this->args['active']) || $this->args['active'] === '') {
            $this->query['select'][] =
                "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active";
        }
        return $this;
    }

    private function addSelectPriotitizeExactCall()
    {
        if (isset($this->args['call']) && $this->args['call'] !== '') {
            $this->query['select'][] =
                "(CASE WHEN s.call = :call THEN 1 ELSE 0 END) AS _call";
            $this->query['param']['call'] = $this->args['call'];
        }
        return $this;
    }

    private function addSelectPrioritizeNonEmpty()
    {
        $column = $this->signalsColumns[$this->args['sort']]['sort'];
        switch($column) {
            case "" :
                break;
            case "range_deg" :
            case "range_km" :
            case "range_mi" :
                $this->query['select'][] =
                    "(CASE WHEN (s.lat is null or s.lat = 0) AND (s.lon is null or s.lon = 0) THEN 1 ELSE 0 END) AS _empty";
                break;
            default:
                $this->query['select'][] =
                    "(CASE WHEN " . $column . " = '' OR " . $column . " IS NULL THEN 1 ELSE 0 END) AS _empty";
                break;
        }
        return $this;
    }

    private function addSelectColumnsAllSignal()
    {
        $distinct = (
            isset($this->args['listener']) ||
            isset($this->args['heard_in']) ||
            isset($this->args['logged_date_1']) ||
            isset($this->args['logged_date_2'])
        );
        $this->query['select'][] = ($distinct ? 'DISTINCT ' : '') . 's.*';
        return $this;
    }

    private function addSelectColumnsAllSignalSeeklist()
    {
        $distinct = (
            isset($this->args['listener']) ||
            isset($this->args['heard_in']) ||
            isset($this->args['logged_date_1']) ||
            isset($this->args['logged_date_2'])
        );
        $this->query['select'][] =
            ($distinct ? 'DISTINCT ' : '')
            . "s.id, s.call, s.khz, s.type, s.active, s.sp, s.itu";
        return $this;
    }

    private function addSelectColumnCountSignal()
    {
        $distinct = (
            isset($this->args['listener']) ||
            isset($this->args['heard_in']) ||
            isset($this->args['logged_date_1']) ||
            isset($this->args['logged_date_2'])
        );
        $this->query['select'][] = "COUNT(" . ($distinct ? "DISTINCT s.id" : "*"). ") AS count";
        return $this;
    }

    private function buildInParamsList($key, $values, $prefix = '', $suffix = '')
    {
        $in = [];
        foreach($values as $idx => $value) {
            $param = $key . '_' . $idx;
            $in[] = ":" . $param;
            $this->query['param'][$param] = $prefix . $value . $suffix;
        }
        return $in;
    }

    private function buildQuery()
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

    public function debugQuery($sql, $params)
    {
        $sql_view = $sql;
        foreach ($params as $key => $value) {
            $sql_view = str_replace(':' . $key, "***'" . $value . "'***", $sql_view);
        }
        return "<pre>" . $sql_view . "</pre>";
    }

    public function getColumns()
    {
        return $this->signalsColumns;
    }

    public function getFilteredSignals($system, $args)
    {
//        die("<pre>".print_r($args, true)."</pre>");
        $this->args = $args;
        $this
            ->setArgs($system, $args)

            ->addFilterActive()
            ->addFilterCall()
            ->addFilterChannels()
            ->addFilterFreq()
            ->addFilterGsq()
            ->addFilterHeardIn()
            ->addFilterListeners()
            ->addFilterLoggedDate()
            ->addFilterLoggedFirst()
            ->addFilterLoggedLast()
            ->addFilterRange()
            ->addFilterRegion()
            ->addFilterStatesAndCountries()
            ->addFilterSystem()
            ->addFilterTypes()

            ->addFromTables();

        switch ($this->args['show']) {
            case 'seeklist':
                $this
                    ->addSelectColumnsAllSignalSeeklist()
                    ->addSelectColumnPersonalise()
                    ->addOrderForSeeklist();
                break;
            default:
                $this
                    ->addSelectColumnsAllSignal()
                    ->addSelectColumnPersonalise()
                    ->addSelectColumnsOffsets()
                    ->addSelectColumnRangeDeg()
                    ->addSelectColumnRangeKm()
                    ->addSelectColumnRangeMiles()
                    ->addSelectPrioritizeNonEmpty()
                    ->addSelectPriotitizeActive()
                    ->addSelectPriotitizeExactCall()

                    ->addOrderPrioritizeSelected()
                    ->addOrderPrioritizeExactCall()
                    ->addOrderPrioritizeActive()

                    ->addLimit($args);
                break;
        }

        $sql = $this->buildQuery();

        $stmt = $this->connection->prepare($sql);

        if ($this->debug) {
            print $this->debugQuery($sql, $this->query['param']);
        }

        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getFilteredSignalsCount($system, $args)
    {
        $this
            ->setArgs($system, $args)

            ->addSelectColumnCountSignal()

            ->addFromTables()

            ->addFilterActive()
            ->addFilterCall()
            ->addFilterChannels()
            ->addFilterFreq()
            ->addFilterGsq()
            ->addFilterHeardIn()
            ->addFilterListeners()
            ->addFilterLoggedDate()
            ->addFilterLoggedFirst()
            ->addFilterLoggedLast()
            ->addFilterRange()
            ->addFilterRegion()
            ->addFilterStatesAndCountries()
            ->addFilterSystem()
            ->addFilterTypes()
        ;

        $sql = $this->buildQuery();

        if ($this->debug) {
            print $this->debugQuery($sql, $this->query['param']);
        }

        $stmt = $this->connection->prepare($sql);
        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getStats($isAdmin)
    {
        $stats = [
            [ 'RNA Only' =>     number_format($this->getCountSignalsRNAOnly()) ],
            [ 'REU Only' =>     number_format($this->getCountSignalsREUOnly()) ],
            [ 'RNA + REU' =>    number_format($this->getCountSignalsRNAAndREU()) ],
            [ 'RWW' =>          number_format($this->getCountSignalsRWW()) ]
        ];
        if ($isAdmin) {
            $stats[] =[ 'Unlogged' => $this->getCountSignalsUnlogged() ];
        }
        return [ 'Signals' => $stats ];
    }

    public static function getSeeklistColumns($signals, $paper)
    {
        $col = 0;
        $col_length = 0;
        $old_place = false;

        $columns = [];
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
            if (!isset($columns[$col])) {
                $columns[$col] = [];
            }
            $columns[$col][] = $s;
            $col_length ++;
        }
        return $columns;
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

    private function getCountSignalsREUOnly()
    {
        $sql =
            "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_an`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getCountSignalsRNAOnly()
    {
        $sql =
            "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_an`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=1 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getCountSignalsRNAAndREU()
    {
        $sql =
            "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_na`=1\n"
            .")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getCountSignalsRWW()
    {
        $sql =
            "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE\n"
            ."    `logs` > 0";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getCountSignalsUnlogged()
    {
        $sql =
            "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_an`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function setArgs($system, $args)
    {
        $this->system = $system;
        if (isset($args['heard_in']) && trim($args['heard_in']) === '') {
            unset($args['heard_in']);
        }
        if (isset($args['listener']) && (!$args['listener'] || in_array('', $args['listener']))) {
            unset($args['listener']);
        }
        if (isset($args['listener_invert']) && $args['listener_invert'] === 0) {
            unset($args['listener_invert']);
        }
        $this->args = $args;
        return $this;
    }
}
