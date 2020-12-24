<?php

namespace App\Repository;

use App\Entity\LogSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogsessionRepository extends ServiceEntityRepository
{
    /**
     * LogSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, LogSession::class);
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
        $listenerID,
        $logs
    ) {
        $logsession = new LogSession();
        $logsession
            ->setTimestamp($timestamp)
            ->setAdministratorId($administratorID)
            ->setListenerId($listenerID)
            ->setLogs($logs);
        $this->getEntityManager()->persist($logsession);
        $this->getEntityManager()->flush();
        return $logsession->getId();
    }
}
