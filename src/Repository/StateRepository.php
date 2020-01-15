<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Sp as SpEntity;

/**
 * Class Region
 * @package App\Service
 */
class StateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpEntity::class);
    }

    public function getMatchingOptions($itu = false)
    {
        $states = $this->getStates($itu);
        $out = [ '' => '' ];
        foreach ($states as $row) {
            $out[$row->getName()] = $row->getSp();
        }

        return $out;
    }

    public function getStates($itu = false)
    {
        $qb =
            $this
                ->createQueryBuilder('sp');
        if ($itu) {
            $qb
                ->where('sp.itu IN(:filter)')
                ->setParameter('filter', $itu);
        }
        return
            $qb
                ->orderBy('sp.sp', 'ASC')
                ->getQuery()
                ->execute();
    }
}
