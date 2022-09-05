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

    private $tabs = [
        ['logsession', 'Overview'],
        ['logsession_stats', 'Stats'],
        ['logsession_logs', 'Logs (%%logs%%)'],
        ['logsession_signals', 'Signals (%%signals%%)'],
        ['logsession_signalsmap', 'Signals Map'],
    ];

    public function getTabs($logsession = false, $isAdmin = false)
    {
        if (!$logsession->getId()) {
            return [];
        }
        $logs =                 $logsession->getLogs();
        $signals =              $logsession->getSignals();
        $stats =                $logsession->getUploadStats() ? unserialize($logsession->getUploadStats()) : false;
        $out = [];
        foreach ($this->tabs as $idx => $data) {
            $route = $data[0];
            switch ($route) {
                case 'logsession_logs':
                    if ($logs) {
                        $out[] = str_replace(
                            ['%%logs%%'],
                            [$logs],
                            $data
                        );
                    }
                    break;
                case 'logsession_stats':
                    if ($stats) {
                        $out[] = $data;
                    }
                    break;
                case 'logsession_signals':
                    if ($signals) {
                        $out[] = str_replace(
                            ['%%signals%%'],
                            [$signals],
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
        $operatorID,
        $comment,
        $entries,
        $stats,
        $status
    ) {
        $logsession = new LogSession();
        $logsession
            ->setTimestamp($timestamp)
            ->setAdministratorId($administratorID)
            ->setListenerId($listenerID)
            ->setOperatorId($operatorID)
            ->setComment($comment)
            ->setUploadEntries(serialize($entries))
            ->setUploadCount(count($entries))
            ->setUploadCursor(0)
            ->setUploadPercent(0)
            ->setUploadStats(serialize($stats))
            ->setUploadStatus($status);

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
            . 'li.active,'
            . '(CASE WHEN li.name IS NULL THEN \'\' ELSE CONCAT(li.name, \' | \', li.qth, \' \', li.sp, \' \', li.itu, \' \', li.gsq) END) as listener,'
            . 'li.website as website,'
            . 'li.callsign as callsign,'
            . 'li.qth as qth,'
            . 'li.sp as sp,'
            . 'li.itu as itu,'
            . 'trim(ls.timestamp) as timestamp,'
            . 'u.name as uploader,'
            . 'u.id as uploaderId,'
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
            . 'ls.comment,'
            . 'ls.signals,'
            . 'ls.uploadStatus,'
            . 'ls.uploadPercent,'
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
                ->andWhere('ls.listenerId = :listenerID')
                ->setParameter('listenerID', $args['listenerId']);
        }

        if (isset($args['operatorId']) && $args['operatorId'] !== '') {
            $qb
                ->andWhere('ls.operatorId = :operatorId')
                ->setParameter('operatorId', $args['operatorId']);
        }

        if (isset($args['administratorId']) && $args['administratorId'] !== '') {
            $qb
                ->andWhere('ls.administratorId = :administratorId')
                ->setParameter('administratorId', $args['administratorId']);
        }

        if (isset($args['limit']) && (int)$args['limit'] !== -1 && isset($args['page'])) {
            $qb
                ->setFirstResult($args['page'] * $args['limit'])
                ->setMaxResults($args['limit']);
        }
        if (isset($args['sort']) && $reportColumns[$args['sort']]['sort']) {
            $idx = $reportColumns[$args['sort']];

            $qb->addSelect(
                "(CASE WHEN ".$idx['sort']." IS NULL OR ".$idx['sort']." = '' THEN 1 ELSE 0 END) AS _blank"
            )
            ->addOrderBy(
                '_blank',
                'ASC'
            )
            ->addOrderBy(
                ($idx['sort']),
                ($args['order'] === 'd' ? 'DESC' : 'ASC')
            );
        }
        $result = $qb->getQuery()->execute();
//        print "<pre>".print_r($qb->getQuery()->getSQL(), true)."</pre>";
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
