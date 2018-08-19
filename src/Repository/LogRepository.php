<?php

namespace App\Repository;

use App\Entity\Log;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getLogsForListener($listenerID)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('l.date, l.time, s.khz, s.call')
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
        return $result;
    }

}
