<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Sp as SpEntity;

/**
 * Class Region
 * @package App\Service
 */
class StateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpEntity::class);
    }

    public function getStates($itu = null)
    {
        return
            $this
                ->createQueryBuilder('sp')
                ->where('sp.itu IN(:filter)')
                ->setParameter('filter', $itu)
                ->getQuery()
                ->execute();
    }
}
