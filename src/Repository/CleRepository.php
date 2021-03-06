<?php

namespace App\Repository;

use App\Entity\Cle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CleRepository extends ServiceEntityRepository
{
    /**
     * CleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cle::class);
    }
}
