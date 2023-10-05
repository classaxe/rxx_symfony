<?php
namespace App\Repository;

use App\Entity\Stats as StatsEntity;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class Region
 * @package App\Service
 */
class StatsRepository extends ServiceEntityRepository
{
    private $connection;
    private $cache = null;
    private $age = 5;

    /**
     * StatsRepository constructor.
     * @param ManagerRegistry $registry
     * @param Connection $connection
     */
    public function __construct(
        ManagerRegistry $registry,
        Connection $connection
    ) {
        parent::__construct($registry, StatsEntity::class);
        $this->connection = $connection;
    }

    public function getChannels($minDate = false, $minTimes = 0)
    {
        $sql = "
            SELECT
                s.khz,
                l.region,
                COUNT(distinct s.id) as stations
            FROM
                signals s
            INNER JOIN logs l on
                l.signalID = s.id
            WHERE
                s.active = 1        -- Is active
                AND s.type = 0      -- Is NDB
                AND s.khz >= 190    -- Is 190 KHz and up
                AND s.khz <= 1720   -- Is 1720 KHz and below
                AND s.id in(        -- Has been heard this year
                    SELECT
                        ls.signalId
                    from
                        logs ls
                    " . ($minDate ? "WHERE ls.date >= '" . $minDate ."'" : "") . "
                    group by
                        ls.signalId
                    " . ($minTimes ? "HAVING COUNT(*) >= " . $minTimes :  "") ."
                )
            GROUP BY
                khz, region
            ORDER BY
                khz, region";

        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAllAssociative();    
    }

    public function getStats()
    {
        if ($this->cache === null) {
            /** @var Doctrine\DBAL\Driver\Statement $stmt */
            $stmt = $this->connection->prepare('select * from stats where ID=1');
            $stmt->execute();
            $this->cache = $stmt->fetchAssociative();
        }
        $timestamp = (new DateTime())->modify('-' . $this->age . 'minute')->format('Y-m-d H:i:s');
        if ($timestamp > $this->cache['timestamp']) {
            $data = [];
            $this->getListenerStats($data);
            $this->getLogStats($data);
            $this->getSignalStats($data);
            ksort($data);
            $timestamp = (new DateTime())->format('Y-m-d H:i:s');
            $sql =
                "REPLACE INTO stats (\n"
                . "    `ID`,\n    `" . implode("`,\n    `", array_keys($data)) . "`,\n    `timestamp`\n"
                . ") VALUES (\n"
                . "    1,\n    '" . implode("',\n    '", array_values($data)) . "',\n    '$timestamp'\n"
                . ")";

            /** @var Doctrine\DBAL\Driver\Statement $stmt */
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();

            /** @var Doctrine\DBAL\Driver\Statement $stmt */
            $stmt = $this->connection->prepare('select * from stats where ID=1');
            $stmt->execute();
            $this->cache = $stmt->fetchAssociative();
        }
        unset ($this->cache['ID']);
        return $this->cache;
    }

    public function getListenerStats(&$data)
    {
        $sql = <<< EOD
            SELECT
                'rna' AS `system`,
                '' AS `region`,
                COUNT(*) as `count`,
                MIN(li_1.log_earliest) AS `first`,
                MAX(li_1.log_latest) AS `last`
            FROM
                listeners li_1
            WHERE
                li_1.region IN ('na','ca')
            UNION SELECT
                'rww',
                li_2.region,
                COUNT(*),
                MIN(li_2.log_earliest),
                MAX(li_2.log_latest)
            FROM
                listeners li_2
            GROUP BY
                li_2.region
EOD;
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAllAssociative();

        foreach ($rows as $row) {
            $key = $row['system'] . ($row['region'] ? '_' . $row['region'] : '');
            $data['listeners_' . $key] = $row['count'];
            $data['log_first_' . $key] = $row['first'];
            $data['log_last_' . $key] = $row['last'];
        }
        $data['listeners_rww'] =    (array_sum(array_column($rows, 'count'))) - $data['listeners_rna'];
        $data['listeners_reu'] =    $data['listeners_rww_eu'];
        $data['log_first_rww'] =    min(array_column($rows, 'first'));
        $data['log_first_reu'] =    $data['log_first_rww_eu'];
        $data['log_last_rww'] =     max(array_column($rows, 'last'));
        $data['log_last_reu'] =     $data['log_last_rww_eu'];
    }

    public function getLogStats(&$data)
    {
        $sql = <<< EOD
            SELECT
                'rna' AS `system`,
                '' AS `region`,
                COUNT(*) as `logs`
            FROM
                logs lo_1
            WHERE
                lo_1.region IN ('na','ca')
            UNION SELECT
                'rww',
                lo_2.region,
                COUNT(*)
            FROM
                logs lo_2
            GROUP BY
                lo_2.region
EOD;
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAllAssociative();

        foreach ($rows as $row) {
            $key = $row['system'] . ($row['region'] ? '_' . $row['region'] : '');
            $data['logs_' . $key] = $row['logs'];
        }
        $data['logs_rww'] =    (array_sum(array_column($rows, 'logs'))) - $data['logs_rna'];
        $data['logs_reu'] =    $data['logs_rww_eu'];
    }

    public function getSignalStats(&$data)
    {
        $regions =      ['af', 'an', 'as', 'ca', 'eu', 'iw', 'na', 'oc', 'sa'];
        $sql = <<< EOD
            SELECT
                'signals_reu' AS stat,
                COUNT(*) AS count
            FROM
                `signals`
            WHERE
                `heard_in_af`=0 AND `heard_in_an`=0 AND `heard_in_as`=0 AND `heard_in_ca`=0 AND `heard_in_eu`=1 AND
                `heard_in_iw`=0 AND `heard_in_na`=0 AND `heard_in_oc`=0 AND `heard_in_sa`=0
            UNION SELECT
                'signals_rna',
                COUNT(*)
            FROM
                `signals`
            WHERE
                `heard_in_af`=0 AND `heard_in_an`=0 AND `heard_in_as`=0 AND `heard_in_eu`=0 AND
                `heard_in_iw`=0 AND `heard_in_oc`=0 AND `heard_in_sa`=0 AND (`heard_in_ca`=1 OR `heard_in_na`=1)
            UNION SELECT
                'signals_rna_reu',
                COUNT(*)
            FROM
                `signals`
            WHERE
                `heard_in_eu`=1 AND (`heard_in_ca`=1 OR `heard_in_na`=1)
            UNION SELECT
                'signals_rww',
                COUNT(*)
            FROM
                `signals`
            WHERE
                `logs` > 0
            UNION SELECT
                'signals_unlogged',
                COUNT(*)
            FROM
                `signals`
            WHERE
                `heard_in_af`=0 AND `heard_in_an`=0 AND `heard_in_as`=0 AND `heard_in_ca`=0 AND `heard_in_eu`=0 AND
                `heard_in_iw`=0 AND `heard_in_na`=0 AND `heard_in_oc`=0 AND `heard_in_sa`=0
EOD;
        foreach ($regions as $region) {
            $sql .= <<< EOD
            UNION SELECT
                'signals_rww_{$region}',
                COUNT(*)
            FROM
                `signals`
            WHERE
                `heard_in_{$region}`=1
EOD;
        }
        /** @var Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAllAssociative();
        foreach ($results as $r) {
            $data[$r['stat']] = (int) $r['count'];
        }
    }
}
