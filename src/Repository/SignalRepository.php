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
    private $connection;
    private $query = [
        'from' =>   [],
        'limit' =>  [],
        'order' =>  [],
        'param' =>  [],
        'select' => [],
        'where' =>  [],
    ];
    private $parameters = [];
    private $sql = '';
    private $queryBuilder;
    private $signalsColumns;

    public function __construct(
        RegistryInterface $registry,
        Connection $connection,
        SignalsColumns $signalsColumns

    ) {
        parent::__construct($registry, Signal::class);
        $this->connection = $connection;
        $this->signalsColumns = $signalsColumns->getColumns();
    }

    private function addFilterCall($call)
    {
        if (isset($call) && $call !== '') {
            $this->query['where'][] ='(s.call LIKE :like_call)';
            $this->query['param']['like_call'] = '%'.$call.'%';
        }
        return $this;
    }

    private function addFilterChannels($channels)
    {
        switch ($channels) {
            case 1:
                $this->query['where'][] ='MOD(s.khz * 1000, 1000) = 0';
                break;
            case 2:
                $this->query['where'][] ='MOD(s.khz * 1000, 1000) != 0';
                break;
        }
        return $this;
    }

    private function addFilterFreq($khz_1, $khz_2)
    {
        $khz_1 = (float)$khz_1 ? (float)$khz_1 : 0;
        $khz_2 = (float)$khz_2 ? (float)$khz_2 : 1000000;

        if ($khz_1 !== 0 || $khz_2 !== 1000000) {
            $this->query['where'][] ='(s.khz BETWEEN :khz1 AND :khz2)';
            $this->query['param']['khz1'] = $khz_1;
            $this->query['param']['khz2'] = $khz_1;
        }
        return $this;
    }

    private function addFilterGsq($gsq)
    {
        if (isset($gsq) && $gsq !== '') {
            $gsq = explode(" ", str_replace('*', '%', $gsq));
            $this->query['where'][] = "(s.gsq LIKE '" . implode($gsq, "%' OR s.gsq LIKE '") . "%')";
        }
        return $this;
    }

    private function addFilterRegion($region)
    {
        if (isset($region) && $region !== '') {
            $this->query['where'][] = '(s.region = :region)';
            $this->query['param']['region'] = $region;
        }
        return $this;
    }

    private function addFilterStatesAndCountries($states, $countries, $sp_itu_clause)
    {
        $clauses = [];
        if (isset($countries) && $countries !== '') {
            $countries = explode(" ", str_replace('*', '%', $countries));
            $clauses[] = "(s.itu LIKE '" . implode($countries, "' OR s.itu LIKE '") . "')";
        }
        if (isset($states) && $states !== '') {
            $states = explode(" ", str_replace('*', '%', $states));
            $clauses[] = "(s.sp LIKE '" . implode($states, "' OR s.sp LIKE '") . "')";
        }
        switch (count($clauses)) {
            case 0:
                break;
            case 1:
                $this->query['where'][] = $clauses[0];
                break;
            case 2:
                $this->query['where'][] = '(' . $clauses[0] . $sp_itu_clause . $clauses[1] . ')';
                break;
        }
        return $this;
    }

    private function addFilterSystem($system)
    {
        switch ($system) {
            case "reu":
                $this->query['where'][] ='(s.heard_in_eu = 1)';
                break;
            case "rna":
                $this->query['where'][] ='(s.heard_in_na = 1) or (s.heard_in_ca = 1)';
                break;
        }
        return $this;
    }

    private function addFilterTypes($types)
    {
        $this->query['where'][] =
            "s.type IN("
            .implode(',', $types)
            .")";
        return $this;
    }

    private function addFrom($from)
    {
        $this->query['from'][] = $from;
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
    }

    private function addSelectColumnActive()
    {
        $this->query['select'][] =
            "(CASE WHEN s.active = 0 THEN 1 ELSE 0 END) AS _active";
        return $this;
    }

    private function addSelectColumnCurrentEmpty($column)
    {
        if ($column) {
            $this->query['select'][] =
                "(CASE WHEN " . $column . " = '' OR " . $column . " IS NULL THEN 1 ELSE 0 END) AS _empty";
        }
        return $this;
    }

    private function addSelectColumnRangeDeg($args) {
        if (isset($args['range_gsq']) && $args['range_gsq'] !== '' && $lat_lon = Rxx::convertGsqToDegrees($args['range_gsq'])) {
            $this->query['select'][] =
                  "CAST(\n"
                . "  COALESCE(\n"
                . "    ROUND(\n"
                . "      DEGREES(\n"
                . "        ATAN2(\n"
                . "          (SIN(RADIANS(s.lon) - RADIANS(:lon)) * COS(RADIANS(s.lat))),\n"
                . "          ((COS(RADIANS(:lat) * SIN(RADIANS(s.lat)))) - SIN(RADIANS(:lat)) * COS(RADIANS(s.lat)) * COS(RADIANS(s.lon) - RADIANS(:lon)))\n"
                . "        )\n"
                . "      ) + 360\n"
                . "    ), ''\n"
                . "  ) AS UNSIGNED\n"
                . ") AS range_deg";
            $this->query['param']['lat'] = $lat_lon['lat'];
            $this->query['param']['lon'] = $lat_lon['lon'];
        }
        return $this;
    }

    private function addSelectColumnRangeKm($args) {
        if (isset($args['range_gsq']) && $args['range_gsq'] !== '' && $lat_lon = Rxx::convertGsqToDegrees($args['range_gsq'])) {
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
        }
        return $this;
    }

    private function addSelectColumnRangeMiles($args) {
        if (isset($args['range_gsq']) && $args['range_gsq'] !== '' && $lat_lon = Rxx::convertGsqToDegrees($args['range_gsq'])) {
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
        }
        return $this;
    }

    private function addSelectColumnExactCall($args)
    {
        if (isset($args['call']) && $args['call'] !== '') {
            $this->query['select'][] =
                "(CASE WHEN s.call = :call THEN 1 ELSE 0 END) AS _call";
            $this->query['param']['call'] = $args['call'];
        }
        return $this;
    }

    private function addSelectColumnsAllSignal()
    {
        $this->query['select'][] = "s.*";
        return $this;
    }

    private function addSelectColumnCountSignal()
    {
        $this->query['select'][] = "COUNT(*) AS `count`";
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
            .($this->query['order'] ? "LIMIT\n    ".implode("\n    ", $this->query['order'])."\n" : "")
            .($this->query['limit'] ? "LIMIT\n    ".implode("\n    ", $this->query['limit'])."\n" : "");

        $this->query['from'] =      [];
        $this->query['limit'] =     [];
        $this->query['order'] =     [];
        $this->query['select'] =    [];
        $this->query['where'] =     [];

        return $sql;
    }

    public function getColumns()
    {
        return $this->signalsColumns;
    }

    public function getFilteredSignals($system, $args)
    {
//        die($args['sort']);

        $this
            ->addSelectColumnsAllSignal()
            ->addSelectColumnActive()
            ->addSelectColumnExactCall($args)
            ->addSelectColumnRangeDeg($args)
            ->addSelectColumnRangeKm($args)
            ->addSelectColumnRangeMiles($args)
            ->addSelectColumnCurrentEmpty($this->signalsColumns[$args['sort']]['sort'])

            ->addFrom('signals s')

            ->addFilterSystem($system)
            ->addFilterTypes($args['signalTypes'])
            ->addFilterCall($args['call'])
            ->addFilterChannels($args['channels'])
            ->addFilterFreq($args['khz_1'], $args['khz_2'])
            ->addFilterStatesAndCountries($args['states'], $args['countries'], $args['sp_itu_clause'])
            ->addFilterRegion($args['region'])
            ->addFilterGsq($args['gsq'])

            ->addLimit($args);

        $sql = $this->buildQuery();

        $stmt = $this->connection->prepare($sql);
        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];

        $stmt->execute();
        $result = $stmt->fetchAll();
//        print "<pre>$sql</pre>"; die;
        return $result;

/*
        if (isset($args['call']) && $args['call'] !== '') {
            $this
                ->getQueryBuilder()
                ->addOrderBy(
                    '_call',
                    'DESC'
                );

        }

        $this
            ->getQueryBuilder()
            ->addOrderBy(
                '_active',
                'ASC'
            );

        if ($this->signalsColumns[$args['sort']]['sort']) {
            $this
                ->getQueryBuilder()
                ->addOrderBy(
                    '_nonempty',
                    'DESC'
                )
                ->addOrderBy(
                    ($this->signalsColumns[$args['sort']]['sort']),
                    ($args['order'] == 'd' ? 'DESC' : 'ASC')
                );
        }
*/
        $result =
            $this
                ->getQueryBuilder()
                ->getQuery()
                ->execute();
//        print Rxx::y($result[0]);

        // Necessary to resolve extra nesting in results caused by extra select to ignore empty fields in sort order
        $out = [];
        foreach ($result as $key => $value) {
            $signal =   $value[0];
            $signal
                ->setRangeKm(isset($value['range_km'])   ? $value['range_km']  : null)
                ->setRangeMi(isset($value['range_mi'])   ? $value['range_mi']  : null)
                ->setRangeDeg(isset($value['range_deg']) ? $value['range_deg'] : null);

            $out[] =    $signal;
        }
        return $out;
    }

    public function getFilteredSignalsCount($system, $args)
    {
        $this
            ->addSelectColumnCountSignal()

            ->addFrom('signals s')

            ->addFilterSystem($system)
            ->addFilterTypes($args['signalTypes'])
            ->addFilterCall($args['call'])
            ->addFilterChannels($args['channels'])
            ->addFilterFreq($args['khz_1'], $args['khz_2'])
            ->addFilterStatesAndCountries($args['states'], $args['countries'], $args['sp_itu_clause'])
            ->addFilterRegion($args['region'])
            ->addFilterGsq($args['gsq'])
        ;

        $sql = $this->buildQuery();

        $stmt = $this->connection->prepare($sql);
        foreach ($this->query['param'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $this->query['param'] = [];
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result[0]['count'];
    }

}
