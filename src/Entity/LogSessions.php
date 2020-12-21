<?php

namespace App\Entity;

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
class LogSessions
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
    private $administratorid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="listenerID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listenerid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logs;


}
