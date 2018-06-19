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

    public function getAllRegions()
    {
        return $this
            ->createQueryBuilder('region')
            ->orderBy('region.name', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function getAllRegionOptions()
    {
        $regions = $this->getAllRegions();
        $out = ['(All Regions)' => ''];
        foreach ($regions as $row) {
            $out[$row->getName()] = $row->getRegion();
        }
        return $out;
    }
}
