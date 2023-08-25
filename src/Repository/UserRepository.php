<?php

namespace App\Repository;

use App\Columns\Users as UsersColumns;
use App\Columns\UserLogsessions as LogsessionsColumns;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    private $connection;
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
        Connection $connection,
        ManagerRegistry $registry,
        UsersColumns $usersColumns,
        LogsessionsColumns $logsessionsColumns

    ) {
        parent::__construct($registry, User::class);
        $this->connection = $connection;
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
        if (!is_object($user) || !$user->getId()) {
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

    public function updateUserStats($userId = false)
    {
        $sql = <<< EOT
UPDATE
	users u
SET    
    count_log =                (SELECT COUNT(*) FROM logs WHERE logs.logSessionID IN(SELECT ID from log_sessions where administratorID = u.ID)),
    count_log_session =        (SELECT COUNT(*) FROM log_sessions WHERE log_sessions.administratorID = u.ID)
EOT;
        if ($userId) {
            $sql .= "\nWHERE\n    u.ID = $userId";
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
