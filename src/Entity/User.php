<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * Sp
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    const INACTIVE =    'INACTIVE_USER';
    const INVALID =     'INVALID_USER';
    const UNKNOWN =     'UNKNOWN_USER';

    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="admin", type="boolean", nullable=false)
     */
    private $admin = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="count_log", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countLog = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="count_log_session", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countLogSession = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=40, nullable=false)
     */
    private $email = '';

    /**
     * @var DateTime
     *
     * @ORM\Column(name="log_earliest", type="date", nullable=true)
     */
    private $logEarliest = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="log_latest", type="date", nullable=true)
     */
    private $logLatest = null;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, nullable=false)
     */
    private $username = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getAdmin(): int
    {
        return $this->admin;
    }

    /**
     * @return int
     */
    public function getCountLog(): int
    {
        return $this->countLog;
    }

    /**
     * @return int
     */
    public function getCountLogSession(): int
    {
        return $this->countLogSession;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return DateTime
     */
    public function getLogEarliest(): DateTime
    {
        return $this->logEarliest;
    }

    /**
     * @return DateTime
     */
    public function getLogLatest(): DateTime
    {
        return $this->logLatest;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $active
     * @return self
     */
    public function setActive($active): self
    {
        $this->active = (int)$active;
        return $this;
    }

    /**
     * @param $admin
     * @return self
     */
    public function setAdmin($admin): self
    {
        $this->admin = (int)$admin;
        return $this;
    }

    /**
     * @param int $countLog
     * @return self
     */
    public function setCountLog(int $countLog): self
    {
        $this->countLog = $countLog;
        return $this;
    }

    /**
     * @param int $countLogSession
     * @return self
     */
    public function setCountLogSession(int $countLogSession): self
    {
        $this->countLogSession = $countLogSession;
        return $this;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param DateTimeInterface $logEarliest
     * @return self
     */
    public function setLogEarliest(DateTimeInterface $logEarliest): self
    {
        $this->logEarliest = $logEarliest;
        return $this;
    }

    /**
     * @param DateTimeInterface $logLatest
     * @return self
     */
    public function setLogLatest(DateTimeInterface $logEarliest): self
    {
        $this->logEarliest = $logEarliest;
        return $this;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

}
