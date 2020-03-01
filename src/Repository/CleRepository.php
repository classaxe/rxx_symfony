<?php

namespace App\Repository;

use App\Entity\Cle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CleRepository extends ServiceEntityRepository
{
    const AWARDSPEC = [
        'DAYTIME' => [
            [ 250,  499,  402,  804 ],
            [ 500,  749,  805,  1205 ],
            [ 750,  999,  1206, 1607 ],
            [ 1000, 1249, 1608, 2010 ],
            [ 1250, 0,    1608, 0 ]
        ]
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
