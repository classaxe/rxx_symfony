<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegionRepository extends ServiceEntityRepository
{
    private $cacheOne = [];
    private $cacheMany = [];
    private $country;

    public function __construct(
        ManagerRegistry $registry,
        CountryRepository $country
    ) {
        parent::__construct($registry, Region::class);
        $this->country = $country;
    }

    public function getOne($code)
    {
        if (isset($this->cacheOne[$code])) {
            return $this->cacheOne[$code];
        }
        $this->cache[$code] = $this
            ->createQueryBuilder('region')
            ->andWhere('region.region = :region')
            ->setParameter('region', $code)
            ->getQuery()
            ->getSingleResult();
        return $this->cacheOne[$code];
    }

    public function getAllOptions($withUnknown = true)
    {
        $regions = $this->getRegions();
        $out = ['(All Regions)' => ''];
        foreach ($regions as $row) {
            if ($withUnknown === false && $row->getRegion() === 'xx') {
                continue;
            }
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

    public function getRegions($regions = null)
    {
        if (isset($this->cacheMany[$regions])) {
            return $this->cacheMany[$regions];
        }
        $qb = $this->createQueryBuilder('r');
        if ($regions) {
            $qb
                ->andWhere(
                    $qb->expr()->in('r.region', ':region')
                )
                ->setParameter('region', explode(',', $regions))
            ;
        }
        $this->cacheMany[$regions] = $qb
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->execute();
        return $this->cacheMany[$regions];
    }
}
