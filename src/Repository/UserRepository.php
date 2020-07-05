<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    /**
     * CleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function logon($username, $password)
    {
        $out = [ 'error' => false, 'record' => false ];

        $r = $this
            ->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();

        if (!$r) {
            $out['error'] = User::UNKNOWN;
            return $out;
        }

        $r = $r[0];

        if (!password_verify($password, $r->getPassword())) {
            $out['error'] = User::INVALID;
            return $out;
        }

        if (!$r->getActive()) {
            $out['error'] = User::INACTIVE;
            return $out;
        }

        $out['record'] = $r;
        return $out;
    }
}
