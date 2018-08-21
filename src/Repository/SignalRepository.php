<?php

namespace App\Repository;

use App\Entity\Signal;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SignalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Signal::class);
    }

    public function getSignalsForListener($listenerID)
    {
        $columns =
             's.id,'
            .'trim(s.khz)+0 as khz,'
            .'s.call,'
            .'s.qth,'
            .'s.sp,'
            .'s.itu,'
            .'s.gsq,'
            .'s.type,'
            .'l.dxKm,'
            .'l.dxMiles,'
            .'COUNT(l.signalid) as logs,'
            .'MAX(l.date) as latest';
        $qb = $this
            ->createQueryBuilder('s')
            ->select($columns)
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.signalid = s.id')
            ->andWhere('l.listenerid = :listenerID')
            ->setParameter('listenerID', $listenerID)
            ->groupBy('s.id')
            ->orderBy(
                's.khz',
                'ASC'
            )
            ->addOrderBy(
                's.call',
                'ASC'
            );
        $result = $qb->getQuery()->execute();
//        print "<pre>".print_r($result, true)."</pre>";
        return $result;
    }
}
