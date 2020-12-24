<?php

namespace App\Entity;

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
     * @var \DateTime|null
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
}
