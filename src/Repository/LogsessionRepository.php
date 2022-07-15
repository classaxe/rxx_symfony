<?php

namespace App\Repository;

use App\Columns\Logsessions as LogsessionsColumns;
use App\Columns\ListenerLogs as LogsColumns;
use App\Entity\LogSession;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

class LogsessionRepository extends ServiceEntityRepository
{
    private $logsessionsColumns;
    private $logsColumns;

    /** @var Connection */
    private $connection;

    /**
     * LogSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        Connection $connection,
        ManagerRegistry $registry,
        LogsessionsColumns $logsessionsColumns,
        LogsColumns $logsColumns
    ) {
        parent::__construct($registry, LogSession::class);
        $this->logsessionsColumns = $logsessionsColumns->getColumns();
        $this->logsColumns = $logsColumns->getColumns();
        $this->connection = $connection;
    }

    /**
     * @param $timestamp
     * @param $administratorID
     * @param $listenerID
     * @param $operatorID
     * @param $logs
     * @return int|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addLogSession(
        $timestamp,
        $administratorID,
        $listenerID,
        $operatorID
    ) {
        $logsession = new LogSession();
        $logsession
            ->setTimestamp($timestamp)
            ->setAdministratorId($administratorID)
            ->setListenerId($listenerID)
            ->setOperatorId($operatorID);
        $this->getEntityManager()->persist($logsession);
        $this->getEntityManager()->flush();
        return $logsession->getId();
    }

    public function getColumns()
    {
        return $this->logsessionsColumns;
    }

    public function getLogsessions(array $args, $reportColumns = [])
    {
        $columns =
            'ls.id,'
            . 'ls.listenerId,'
            . 'li.primaryQth,'
            . '(CASE WHEN li.name IS NULL THEN \'\' ELSE CONCAT(li.name, \' | \', li.qth, \' \', li.sp, \' \', li.itu, \' [\', li.gsq, \']\') END) as listener,'
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
            . 'ls.logsTime,'
            . 'ls.operatorId,'
            . '(CASE WHEN op.name IS NULL THEN \'\' ELSE op.name END) as operator';

        $qb = $this
            ->createQueryBuilder('ls')
            ->select($columns)
            ->innerJoin('\App\Entity\Listener', 'li', 'WITH', 'ls.listenerId = li.id')
            ->innerJoin('\App\Entity\User', 'u', 'WITH', 'ls.administratorId = u.id')
            ->leftJoin('\App\Entity\Listener', 'op', 'WITH', 'ls.operatorId = op.id')
        ;

        if (isset($args['listenerId']) && $args['listenerId'] !== '') {
            $qb
                ->andWhere('li.id = :listenerID')
                ->setParameter('listenerID', $args['listenerId']);
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }

        if (isset($args['sort']) && $reportColumns[$args['sort']]['sort']) {
            $idx = $reportColumns[$args['sort']];
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
