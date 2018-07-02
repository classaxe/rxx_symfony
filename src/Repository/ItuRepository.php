<?php

namespace App\Repository;

use App\Entity\Itu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ItuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Itu::class);
    }

    public function getAllForSystem($system = null)
    {
        $qb = $this->createQueryBuilder('c');
        switch($system) {
            case "reu":
                $qb
                    ->andWhere('c.region = :eu')
                    ->setParameter('eu','eu');
                break;
            case "rna":
                $qb
                    ->andWhere('c.region = :oc')
                    ->andWhere('c.itu = :hwa')
                    ->orWhere('c.region in (:na_ca)')
                    ->setParameter('na_ca', ['na','ca'])
                    ->setParameter('oc', 'oc')
                    ->setParameter('hwa', 'hwa');
                break;
        }

        return $qb
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getAllOptionsForSystem($system = null)
    {
        $countries = $this->getAllForSystem($system);
        $out = ['(All Countries' => ''];
        foreach ($countries as $row) {
            $out[$row->getName()] = $row->getItu();
        }
        return $out;
    }

    public function getAllOptionsForSystemAndRegion($system, $region='')
    {
        $countries = $this->getAllForSystem($system);
        $out = ['(All Countries'.($region ? ' in selected region' : '').')' => ''];
        foreach ($countries as $row) {
            if (!$region || $region === $row->getRegion()) {
                $out[$row->getName()] = $row->getItu();
            }
        }
        return $out;
    }
}
