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
            $this->query['where'][] = "(s.gsq LIKE '" . implode($gsq, "%' OR s.gsq LIKE '") . "%')";
        }
        return $this;
    }

    private function addFilterListeners()
    {
        if (isset($this->args['listener'])) {
            $this->query['where'][] =
                "`l`.`listenerId` IN (" . implode(',', $this->args['listener']) . ")";
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
                        "(CAST(\n"
                        . "  COALESCE(\n"
                        . "    ROUND(\n"
                        . "      DEGREES(\n"
                        . "        ACOS(\n"
                        . "          (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                        . "          (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                        . "        )\n"
                        . "      ) * ".$mult.",\n"
                        . "      2\n"
                        . "    ), ''\n"
                        . "  ) AS UNSIGNED\n"
                        . ") BETWEEN :min AND :max AND\n"
                        . "(s.lat != 0 AND s.lon != 0))";
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
            $clauses[] = "(s.itu LIKE '" . implode($countries, "' OR s.itu LIKE '") . "')";
        }
        if (isset($this->args['states']) && $this->args['states'] !== '') {
            $states = explode(" ", str_replace('*', '%', $this->args['states']));
            $clauses[] = "(s.sp LIKE '" . implode($states, "' OR s.sp LIKE '") . "')";
        }
        switch (count($clauses)) {
            case 0:
                break;
            case 1:
                $this->query['where'][] = $clauses[0];
                break;
            case 2:
                $this->query['where'][] = '(' . $clauses[0] . $this->args['sp_itu_clause'] . $clauses[1] . ')';
                break;
        }
        return $this;
    }

    private function addFilterSystem()
    {
        switch ($this->system) {
            case "reu":
                $this->query['where'][] ='(s.heard_in_eu = 1)';
                break;
            case "rna":
                $this->query['where'][] ='(s.heard_in_na = 1) or (s.heard_in_ca = 1)';
                break;
        }
        return $this;
    }

    private function addFilterTypes()
    {
        $this->query['where'][] =
            "s.type IN("
            .implode(',', $this->args['signalTypes'])
            .")";
        return $this;
    }

    private function addFromTables()
    {
        if (isset($this->args['listener'])) {
            $this->query['from'][] =
                "`signals` `s`\n"
                . "INNER JOIN `logs` `l` ON\n"
                . "    `s`.`id` = `l`.`signalID`";

            return $this;
        }

        $this->query['from'][] =    '`signals` `s`';
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

    private function addOrderPrioritizeActive()
    {
        $this->addOrder('_active','ASC');
        return $this;
    }

    private function addOrderPrioritizeExactCall()
    {
        if ($this->args['call']) {
            $this->addOrder('_call','DESC');
        }
        return $this;
    }

    private function addOrderSelected()
    {
        if ($this->signalsColumns[$this->args['sort']]['sort']) {
            $this
                ->addOrder(
                    '_empty',
                    'ASC'
                )
                ->addOrder(
                    ($this->signalsColumns[$this->args['sort']]['sort']),
                    ($this->args['order'] == 'd' ? 'DESC' : 'ASC')
                );
        }
        return $this;
    }

    private function addSelectPriotitizeActive()
    {
        $this->query['select'][] =
            "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active";
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

    private function addSelectColumnRangeDeg()
    {
        if (isset($this->args['range_gsq']) && $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $this->query['select'][] =
                  "  CAST(\n"
                . "    COALESCE(\n"
                . "      ROUND(\n"
                . "        (\n"
                . "          DEGREES(\n"
                . "            ATAN2(\n"
                . "              (SIN(RADIANS(s.lon) - RADIANS(:lon)) * COS(RADIANS(s.lat))),\n"
                . "              ((COS(RADIANS(:lat)) * SIN(RADIANS(s.lat))) - SIN(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(s.lon) - RADIANS(:lon)))\n"
                . "            )\n"
                . "          ) + 360\n"
                . "        ) MOD 360\n"
                . "      ),\n"
                . "      ''\n"
                . "    ) AS UNSIGNED\n"
                . "  ) AS `range_deg`\n";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS `range_deg`";
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
                . "  COALESCE(\n"
                . "    ROUND(\n"
                . "      DEGREES(\n"
                . "        ACOS(\n"
                . "          (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                . "          (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                . "        )\n"
                . "      ) * ".RXX::DEG_KM_MULTIPLIER.",\n"
                . "      2\n"
                . "    ), ''\n"
                . "  ) AS UNSIGNED\n"
                . ") AS range_km";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS `range_km`";
        }
        return $this;
    }

    private function addSelectColumnRangeMiles() {
        if (isset($this->args['range_gsq']) &&
            $this->args['range_gsq'] !== '' &&
            $lat_lon = Rxx::convertGsqToDegrees($this->args['range_gsq'])
        ) {
            $this->query['select'][] =
                  "CAST(\n"
                . "  COALESCE(\n"
                . "    ROUND(\n"
                . "      DEGREES(\n"
                . "        ACOS(\n"
                . "          (SIN(RADIANS(:lat)) * SIN(RADIANS(s.lat))) + \n"
                . "          (COS(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(:lon - s.lon)))\n"
                . "        )\n"
                . "      ) * ".RXX::DEG_MI_MULTIPLIER.",\n"
                . "      2\n"
                . "    ), ''\n"
                . "  ) AS UNSIGNED\n"
                . ") AS range_mi";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        } else {
            $this->query['select'][] = "NULL AS `range_mi`";
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

    private function addSelectColumnsAllSignal()
    {
        $this->query['select'][] = (isset($this->args['listener']) ? "DISTINCT s.*" : "s.*");
        return $this;
    }

    private function addSelectColumnCountSignal()
    {
        $this->query['select'][] =
            "COUNT(" . (isset($this->args['listener']) ? "DISTINCT s.id" : "*") . ") AS `count`";
        return $this;
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

        if ($this->debug) {
            print "<pre>" . print_r($sql, true) . "</pre>";
        }
        return $sql;
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

            ->addSelectColumnsAllSignal()
            ->addSelectColumnRangeDeg()
            ->addSelectColumnRangeKm()
            ->addSelectColumnRangeMiles()

            ->addSelectPrioritizeNonEmpty()
            ->addSelectPriotitizeActive()
            ->addSelectPriotitizeExactCall()

            ->addFromTables()

            ->addFilterCall()
            ->addFilterChannels()
            ->addFilterFreq()
            ->addFilterGsq()
            ->addFilterListeners()
            ->addFilterRange()
            ->addFilterRegion()
            ->addFilterStatesAndCountries()
            ->addFilterSystem()
            ->addFilterTypes()

            ->addOrderPrioritizeExactCall()
            ->addOrderPrioritizeActive()
            ->addOrderSelected()

            ->addLimit($args);

        $sql = $this->buildQuery();

        $stmt = $this->connection->prepare($sql);
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

            ->addFilterCall()
            ->addFilterChannels()
            ->addFilterFreq()
            ->addFilterGsq()
            ->addFilterListeners()
            ->addFilterRange()
            ->addFilterRegion()
            ->addFilterStatesAndCountries()
            ->addFilterSystem()
            ->addFilterTypes()
        ;

        $sql = $this->buildQuery();

        $stmt = $this->connection->prepare($sql);
        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    private function setArgs($system, $args)
    {
        $this->system = $system;
        if (isset($args['listener']) && in_array('', $args['listener'])) {
            unset($args['listener']);
        }
        $this->args = $args;
        return $this;
    }
}
