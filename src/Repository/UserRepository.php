<?php

namespace App\Repository;

use App\Columns\Users as UsersColumns;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    const defaultlimit =    50;
    const defaultOrder =    'a';
    const defaultPage =     0;
    const defaultSorting =  'name';

    private $usersColumns;

    /**
     * CleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry,
        UsersColumns $usersColumns

    ) {
        parent::__construct($registry, User::class);
        $this->usersColumns = $usersColumns->getColumns();
    }

    public function getColumns()
    {
        return $this->usersColumns;
    }

    public function getCount()
    {
        return $this
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRecords($args)
    {
        return $this
            ->createQueryBuilder('u')
            ->addOrderBy('u.' . $args['sort'], $args['order'] === 'a' ? 'ASC' : 'DESC')
            ->setFirstResult((int)$args['page'] * (int)$args['limit'])
            ->setMaxResults($args['limit'])
            ->getQuery()
            ->getArrayResult();
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
