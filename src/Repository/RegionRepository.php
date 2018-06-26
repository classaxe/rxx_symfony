<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RegionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function get($code)
    {
        return $this
            ->createQueryBuilder('region')
            ->andWhere('region.region = :region')
            ->setParameter('region',$code)
            ->getQuery()
            ->getSingleResult();
    }

    public function getAll()
    {
        return $this
            ->createQueryBuilder('region')
            ->orderBy('region.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getAllOptions()
    {
        $regions = $this->getAll();
        $out = ['(All Regions)' => ''];
        foreach ($regions as $row) {
            $out[$row->getName()] = $row->getRegion();
        }
        return $out;
    }
}
