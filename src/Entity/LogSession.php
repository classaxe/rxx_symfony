<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * LogSessions
 *
 * @ORM\Table(
 *     name="log_sessions",
 *     indexes={
 *          @ORM\Index(name="idx_administratorID", columns={"administratorID"}),
 *          @ORM\Index(name="idx_logs", columns={"logs"}),
 *          @ORM\Index(name="idx_timestamp", columns={"timestamp"}),
 *          @ORM\Index(name="idx_listenerID", columns={"listenerID"})
 *     }
 * )
 * @ORM\Entity
 */
class LogSession
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="administratorID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $administratorId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="first_log", type="datetime", nullable=true)
     */
    private $firstLog;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="last_log", type="datetime", nullable=true)
     */
    private $lastLog;

    /**
     * @var int|null
     *
     * @ORM\Column(name="listenerID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listenerId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logs;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_DGPS", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsDgps;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_DSC", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsDsc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_HAMBCN", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsHambcn;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_NAVTEX", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsNavtex;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_NDB", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsNdb;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_OTHER", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsOther;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs_TIME", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logsTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="operatorID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $operatorId;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @param DateTimeInterface|null $timestamp
     * @return $this
     */
    public function setTimestamp(?DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAdministratorId(): ?int
    {
        return $this->administratorId;
    }

    /**
     * @param int $administratorId
     * @return $this
     */
    public function setAdministratorId(int $administratorId): self
    {
        $this->administratorId = $administratorId;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFirstLog(): ?DateTimeInterface
    {
        return $this->firstLog;
    }

    /**
     * @param DateTimeInterface|null $firstLog
     * @return $this
     */
    public function setFirstLog(?DateTimeInterface $firstLog): self
    {
        $this->firstLog = $firstLog;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastLog(): ?DateTimeInterface
    {
        return $this->lastLog;
    }

    /**
     * @param DateTimeInterface|null $lastLog
     * @return $this
     */
    public function setLastLog(?DateTimeInterface $lastLog): self
    {
        $this->lastLog = $lastLog;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getListenerId(): ?int
    {
        return $this->listenerId;
    }

    /**
     * @param int $listenerId
     * @return $this
     */
    public function setListenerId(int $listenerId): self
    {
        $this->listenerId = $listenerId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogs(): ?int
    {
        return $this->logs;
    }

    /**
     * @param int|null $logs
     * @return $this
     */
    public function setLogs(?int $logs): self
    {
        $this->logs = $logs;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsDgps(): ?int
    {
        return $this->logsDgps;
    }

    /**
     * @param int|null $logsDgps
     * @return $this
     */
    public function setLogsDgps(?int $logsDgps): self
    {
        $this->logsDgps = $logsDgps;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsDsc(): ?int
    {
        return $this->logsDsc;
    }

    /**
     * @param int|null $logsDsc
     * @return $this
     */
    public function setLogsDsc(?int $logsDsc): self
    {
        $this->logsDsc = $logsDsc;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsHambcn(): ?int
    {
        return $this->logsHambcn;
    }

    /**
     * @param int|null $logsHambcn
     * @return $this
     */
    public function setLogsHambcn(?int $logsHambcn): self
    {
        $this->logsHambcn = $logsHambcn;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsNavtex(): ?int
    {
        return $this->logsNavtex;
    }

    /**
     * @param int|null $logsNavtex
     * @return $this
     */
    public function setLogsNavtex(?int $logsNavtex): self
    {
        $this->logsNavtex = $logsNavtex;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsNdb(): ?int
    {
        return $this->logsNdb;
    }

    /**
     * @param int|null $logsNdb
     * @return $this
     */
    public function setLogsNdb(?int $logsNdb): self
    {
        $this->logsNdb = $logsNdb;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsOther(): ?int
    {
        return $this->logsOther;
    }

    /**
     * @param int|null $logsOther
     * @return $this
     */
    public function setLogsOther(?int $logsOther): self
    {
        $this->logsOther = $logsOther;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogsTime(): ?int
    {
        return $this->logsTime;
    }

    /**
     * @param int|null $logsTime
     * @return $this
     */
    public function setLogsTime(?int $logsTime): self
    {
        $this->logsTime = $logsTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOperatorId(): ?int
    {
        return $this->operatorId;
    }

    /**
     * @param int|null $operatorId
     * @return $this
     */
    public function setOperatorId(?int $operatorId): self
    {
        $this->operatorId = $operatorId;

        return $this;
    }
}
