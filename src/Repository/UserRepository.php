<?php

namespace App\Repository;

use App\Columns\Users as UsersColumns;
use App\Columns\UserLogsessions as LogsessionsColumns;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    private $usersColumns;
    private $logsessionsColumns;

    private $tabs = [
        ['user', 'Profile'],
        ['user_logsessions', 'Sessions (%%logsessions%%)'],
    ];

    /**
     * CleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry,
        UsersColumns $usersColumns,
        LogsessionsColumns $logsessionsColumns

    ) {
        parent::__construct($registry, User::class);
        $this->usersColumns = $usersColumns->getColumns();
        $this->logsessionsColumns = $logsessionsColumns->getColumns();
    }

    public function getColumns($mode = '')
    {
        switch ($mode) {
            case 'logsessions':
                return $this->logsessionsColumns;
            case 'users':
                return $this->usersColumns;
        }
        return false;
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
        $qb = $this
            ->createQueryBuilder('u')
            ->addOrderBy('u.' . $args['sort'], $args['order'] === 'a' ? 'ASC' : 'DESC');

        if (is_numeric($args['limit']) && (int)$args['limit'] !== -1) {
            $qb
                ->setFirstResult((int)$args['page'] * (int)$args['limit'])
                ->setMaxResults((int)$args['limit']);
        }
        return  $qb->getQuery()->getArrayResult();
    }

    public function getTabs($user = false, $isAdmin = false)
    {
        if (!$user->getId()) {
            return [];
        }
        $logsessions =          $user->getCountLogSession();
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                case 'user_logsessions':
                    if ($logsessions) {
                        $out[] = str_replace(
                            ['%%logsessions%%'],
                            [$logsessions],
                            $data
                        );
                    }
                    break;
                default:
                    $out[] = $data;
                    break;
            }
        }
        return $out;
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
