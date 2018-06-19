<?php

namespace App\Repository;

use App\Entity\Listener;
use App\Utils\Rxx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ListenerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Listener::class);
    }

    public function getFilteredListeners($system, $arguments)
    {
        $qb = $this->createQueryBuilder('l');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('(l.region = :eu)')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }
        if ($arguments['filter']) {
            $qb
                ->andWhere('(l.name like :filter or l.qth like :filter or l.callsign like :filter)')
                ->setParameter('filter', '%'.$arguments['filter'].'%')
            ;
        }
        if ($arguments['country']) {
            $qb
                ->andWhere('(l.itu = :country)')
                ->setParameter('country', $arguments['country'])
            ;
        }
        if ($arguments['region']) {
            $qb
                ->andWhere('(l.region = :region)')
                ->setParameter('region', $arguments['region'])
            ;
        }
        return $qb
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getTotalListeners($system)
    {
        $qb = $this
            ->createQueryBuilder('l')
            ->select('count(l.id)');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('l.region = :eu')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('(l.region = :oc and l.itu = :hwa) or (l.region in (:na_ca))')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}
