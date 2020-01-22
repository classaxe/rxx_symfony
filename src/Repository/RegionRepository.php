<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegionRepository extends ServiceEntityRepository
{
    private $country;

    public function __construct(
        ManagerRegistry $registry,
        CountryRepository $country
    ) {
        parent::__construct($registry, Region::class);
        $this->country = $country;
    }

    public function get($code)
    {
        return $this
            ->createQueryBuilder('region')
            ->andWhere('region.region = :region')
            ->setParameter('region', $code)
            ->getQuery()
            ->getSingleResult();
    }

    public function getRegions($regions = null)
    {
        $qb = $this
            ->createQueryBuilder('r');
        if ($regions) {
            $qb
                ->andWhere(
                    $qb->expr()->in('r.region', ':region')
                )
                ->setParameter('region', explode(',', $regions))
            ;
        }
        return
            $qb
                ->orderBy('r.name', 'ASC')
                ->getQuery()
                ->execute();
    }

    public function getAllOptions()
    {
        $regions = $this->getRegions();
        $out = ['(All Regions)' => ''];
        foreach ($regions as $row) {
            $out[$row->getName()] = $row->getRegion();
        }
        return $out;
    }

    public function getAllWithCountries($region = '*')
    {
        $regions = $this->getRegions('*' === $region ? '' : $region);
        foreach ($regions as &$region) {
            $code =                 $region->getRegion();
            $region->countries =    $this->country->getMatching(false, $code);
        }
        return $regions;
    }
}
