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
        $qb = $this
            ->createQueryBuilder('s')
            ->innerJoin('\App\Entity\Log', 'l')
            ->andWhere('l.signalid = s.id')
            ->andWhere('l.listenerid = :listenerID')
            ->setParameter('listenerID', $listenerID)
            ->orderBy(
                's.khz',
                'ASC'
            )
            ->addOrderBy(
                's.call',
                'ASC'
            );
        $result = $qb->getQuery()->execute();
        return $result;
    }

}
