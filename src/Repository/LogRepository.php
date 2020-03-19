<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
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
}
