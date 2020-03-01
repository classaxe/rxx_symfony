<?php

namespace App\Repository;

use App\Entity\Cle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CleRepository extends ServiceEntityRepository
{
    const AWARDSPEC = [
        'DAYTIME' => [
            [ 250,   499,  402,  804 ],
            [ 500,   749,  805, 1205 ],
            [ 750,   999, 1206, 1607 ],
            [ 1000, 1249, 1608, 2010 ],
            [ 1250,    0, 1608,    0 ]
        ],
        'LONGRANGER' => [
            [ 500,   999,  805, 1607 ],
            [ 1000, 1499, 1608, 2413 ],
            [ 1500, 1999, 2414, 3217 ],
            [ 2000, 2499, 3218, 4022 ],
            [ 2500, 2999, 4023, 4826 ],
            [ 3000, 4999, 4827, 8044 ],
            [ 5000,    0, 8045,    0 ]
        ],
        'REGION_EU' => [ 10, 20, 30, 40 ],
        'REGION_NA' => [ 10, 30, 45, 60 ],
        'REGION_CA' => [  3, 10, 15, 20 ],
        'REGION_SA' => [  1,  3,  5, 10 ],
        'REGION_AF' => [  1,  5, 10, 15 ],
        'REGION_AS' => [  1,  3,  5, 10 ],
        'REGION_OC' => [  1,  3,  5, 10 ],
        'REGION_AN' => [  1 ],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cle::class);
    }

    public function getAwardSpec($type)
    {
        return static::AWARDSPEC[$type];
    }
}
