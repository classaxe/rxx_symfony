<?php

namespace App\Repository;

use App\Columns\Logsessions as LogsessionsColumns;
use App\Entity\LogSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogsessionRepository extends ServiceEntityRepository
{
    private $logsessionsColumns;

    /**
     * LogSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry,
        LogsessionsColumns $logsessionsColumns
    ) {
        parent::__construct($registry, LogSession::class);
        $this->logsessionsColumns = $logsessionsColumns->getColumns();
    }

    /**
     * @param $timestamp
     * @param $administratorID
     * @param $listenerID
     * @param $logs
     * @return int|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addLogSession(
        $timestamp,
        $administratorID,
        $listenerID
    ) {
        $logsession = new LogSession();
        $logsession
            ->setTimestamp($timestamp)
            ->setAdministratorId($administratorID)
            ->setListenerId($listenerID);
        $this->getEntityManager()->persist($logsession);
        $this->getEntityManager()->flush();
        return $logsession->getId();
    }

    public function getColumns()
    {
        return $this->logsessionsColumns;
    }

    public function getLogsessions(array $args)
    {
        $columns =
            'ls.id,'
            . 'ls.listenerId,'
            . 'li.primaryQth,'
            . 'li.name as listener,'
            . 'li.callsign as callsign,'
            . 'li.qth as qth,'
            . 'li.sp as sp,'
            . 'li.itu as itu,'
            . 'trim(ls.timestamp) as timestamp,'
            . 'u.name as uploader,'
            . 'trim(ls.firstLog) as firstLog,'
            . 'trim(ls.lastLog) as lastLog,'
            . 'ls.logs,'
            . 'ls.logsDgps,'
            . 'ls.logsDsc,'
            . 'ls.logsHambcn,'
            . 'ls.logsNavtex,'
            . 'ls.logsNdb,'
            . 'ls.logsOther,'
            . 'ls.logsTime';

        $qb = $this
            ->createQueryBuilder('ls')
            ->select($columns)
            ->innerJoin('\App\Entity\Listener', 'li', 'WITH', 'ls.listenerId = li.id')
            ->innerJoin('\App\Entity\User', 'u', 'WITH', 'ls.administratorId = u.id');

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $this->logsessionsColumns[$args['sort']]['sort']) {
            $idx = $this->logsessionsColumns[$args['sort']];
            $qb
                ->addOrderBy(
                    ($idx['sort']),
                    ($args['order'] === 'd' ? 'DESC' : 'ASC')
                );
        }

        $result = $qb->getQuery()->execute();
//        print "<pre>".print_r($result, true)."</pre>";
        return $result;
    }

    public function getLogsessionsCount()
    {
        $qb = $this
            ->createQueryBuilder('ls')
            ->select('COUNT(ls.id) as count')
            ->innerJoin('\App\Entity\Listener', 'li', 'WITH', 'ls.listenerId = li.id')
            ->innerJoin('\App\Entity\User', 'u', 'WITH', 'ls.administratorId = u.id');

        return $qb->getQuery()->getSingleScalarResult();
    }

}
