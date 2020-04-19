<?php

namespace App\Repository;

use App\Entity\Log;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

class LogRepository extends ServiceEntityRepository
{
    private $connection;

    /**
     * LogRepository constructor.
     * @param ManagerRegistry $registry
     * @param Connection $connection
     */
    public function __construct(
        ManagerRegistry $registry,
        Connection $connection
    ) {
        parent::__construct($registry, Log::class);
        $this->connection = $connection;
    }

    public function getFilteredLogsCount($system, $region = '')
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('COUNT(l.id) as count');

        $this->addFilterSystem($qb, $system);

        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFirstAndLastLog($system, $region = '')
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('MIN(l.date) AS first, MAX(l.date) AS last');

        $this->addFilterSystem($qb, $system);

        if ($region) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $region);
        }
        return $qb->getQuery()->getArrayResult()[0];
    }

    private function addFilterSystem(&$qb, $system)
    {
        switch ($system) {
            case "reu":
                $qb
                    ->andWhere('(l.region = :eu)')
                    ->setParameter('eu', 'eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :na)')
                    ->setParameter('na', 'na');
                break;
        }
    }

    public function getLogsForListener($listenerID)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select(
                'l.date,'
                .'l.time,'
                .'l.daytime,'
                .'l.dxKm,'
                .'l.dxMiles,'
                .'CONCAT(l.lsbApprox, l.lsb) AS lsb,'
                .'CONCAT(l.usbApprox, l.usb) AS usb,'
                .'l.sec,'
                .'l.format,'
                .'s.khz,'
                .'s.call,'
                .'s.type,'
                .'s.active,'
                .'(CASE WHEN s.pwr = 0 THEN \'\' ELSE s.pwr END) AS pwr,'
                .'s.sp,'
                .'s.itu,'
                .'s.region,'
                .'s.gsq,'
                .'s.lat,'
                .'s.lon,'
                .'s.qth'
            )
            ->andWhere('l.listenerid = :listenerID')
            ->setParameter('listenerID', $listenerID)
            ->innerJoin('\App\Entity\Signal', 's')
            ->andWhere('l.signalid = s.id')
            ->orderBy(
                'l.date',
                'ASC'
            )
            ->addOrderBy(
                'l.time',
                'ASC'
            );
        $result = $qb->getQuery()->execute();
        foreach ($result as &$row) {
            $row['qth'] = str_replace("\"", "\\\"", html_entity_decode($row['qth']));
        }
        return $result;
    }

    public function updateDx($listenerId = false, $signalId = false)
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $chunksize = 200000;

        $WHERE = ($listenerId || $signalId ?
            "WHERE\n"
            . ($listenerId ? "    `logs`.`listenerID` = $listenerId" : '')
            . ($listenerId && $signalId ? " AND\n" : "\n")
            . ($signalId ? "    `logs`.`signalID` = $signalId" : '')
            : ''
        );
        $sql = <<< EOD
            SELECT
                count(*)
            FROM
                `logs`
            INNER JOIN `signals` `s` ON
                `logs`.`signalID` = `s`.`ID`
            INNER JOIN `listeners` `l` ON
                `logs`.`listenerID` = `l`.`ID`
            $WHERE
EOD;

        $stmt =     $this->connection->prepare($sql);
        $stmt->execute();
        $count =    $stmt->fetchColumn();
        $affected = 0;

        for ($offset = 0; $offset < $count; $offset += $chunksize) {
            $sql = <<< EOD
                SELECT
                    `logs`.`ID`,
                    `logs`.`dx_km`,
                    `logs`.`dx_miles`,
                    `s`.`lat` AS `s_lat`,
                    `s`.`lon` AS `s_lon`,
                    `l`.`lat` AS `l_lat`,
                    `l`.`lon` AS `l_lon`
                FROM
                    `logs`
                INNER JOIN `signals` `s` ON
                    `logs`.`signalID` = `s`.`ID`
                INNER JOIN `listeners` `l` ON
                    `logs`.`listenerID` = `l`.`ID`
                $WHERE
                LIMIT $chunksize OFFSET $offset
EOD;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $r) {
                $dx = Rxx::getDx($r['l_lat'], $r['l_lon'], $r['s_lat'], $r['s_lon']);
                if (!$dx) {
                    continue;
                }
                if (($dx['km'] === (int)$r['dx_km']) && ($dx['miles'] === (int)$r['dx_miles'])) {
                    continue;
                }
                $sql =  <<< EOD
                    UPDATE
                        `logs`
                    SET
                        `dx_km` = {$dx['km']},
                        `dx_miles` = {$dx['miles']}
                    WHERE
                        `ID` = {$r['ID']};
EOD;
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                $affected += $stmt->rowCount();
            }
        }
        return $affected;
    }
}
