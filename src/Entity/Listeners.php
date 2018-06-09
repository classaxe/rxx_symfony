<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Listeners
 *
 * @ORM\Table(name="listeners", indexes={@ORM\Index(name="count_logs", columns={"count_logs"}), @ORM\Index(name="name", columns={"name"}), @ORM\Index(name="QTH", columns={"QTH"}), @ORM\Index(name="primary_QTH", columns={"primary_QTH"}), @ORM\Index(name="region", columns={"region"}), @ORM\Index(name="SP", columns={"SP"}), @ORM\Index(name="ITU", columns={"ITU"}), @ORM\Index(name="map_x", columns={"map_x"})})
 * @ORM\Entity
 */
class Listeners
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
     * @var string
     *
     * @ORM\Column(name="callsign", type="string", length=12, nullable=false)
     */
    private $callsign = '';

    /**
     * @var int
     *
     * @ORM\Column(name="count_DGPS", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countDgps = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_DSC", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countDsc = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_HAMBCN", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countHambcn = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_logs", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countLogs = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_NAVTEX", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countNavtex = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_NDB", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countNdb = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_OTHER", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countOther = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_TIME", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countTime = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="count_signals", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $countSignals = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=40, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="equipment", type="text", length=65535, nullable=false)
     */
    private $equipment;

    /**
     * @var string
     *
     * @ORM\Column(name="GSQ", type="string", length=6, nullable=false)
     */
    private $gsq = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ITU", type="string", length=3, nullable=false)
     */
    private $itu = '';

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", precision=10, scale=0, nullable=false)
     */
    private $lat = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="log_format", type="string", length=128, nullable=false)
     */
    private $logFormat = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="log_latest", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $logLatest = '0000-00-00';

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", precision=10, scale=0, nullable=false)
     */
    private $lon = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="map_x", type="smallint", nullable=false)
     */
    private $mapX = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="map_y", type="smallint", nullable=false)
     */
    private $mapY = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=255, nullable=false)
     */
    private $notes = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="primary_QTH", type="boolean", nullable=false)
     */
    private $primaryQth = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="QTH", type="string", length=255, nullable=false)
     */
    private $qth = '';

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=2, nullable=false)
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="SP", type="string", length=6, nullable=false)
     */
    private $sp = '';

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=5, nullable=false)
     */
    private $timezone = '';

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="text", length=65535, nullable=false)
     */
    private $website;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallsign(): ?string
    {
        return $this->callsign;
    }

    public function setCallsign(string $callsign): self
    {
        $this->callsign = $callsign;

        return $this;
    }

    public function getCountDgps(): ?int
    {
        return $this->countDgps;
    }

    public function setCountDgps(int $countDgps): self
    {
        $this->countDgps = $countDgps;

        return $this;
    }

    public function getCountDsc(): ?int
    {
        return $this->countDsc;
    }

    public function setCountDsc(int $countDsc): self
    {
        $this->countDsc = $countDsc;

        return $this;
    }

    public function getCountHambcn(): ?int
    {
        return $this->countHambcn;
    }

    public function setCountHambcn(int $countHambcn): self
    {
        $this->countHambcn = $countHambcn;

        return $this;
    }

    public function getCountLogs(): ?int
    {
        return $this->countLogs;
    }

    public function setCountLogs(int $countLogs): self
    {
        $this->countLogs = $countLogs;

        return $this;
    }

    public function getCountNavtex(): ?int
    {
        return $this->countNavtex;
    }

    public function setCountNavtex(int $countNavtex): self
    {
        $this->countNavtex = $countNavtex;

        return $this;
    }

    public function getCountNdb(): ?int
    {
        return $this->countNdb;
    }

    public function setCountNdb(int $countNdb): self
    {
        $this->countNdb = $countNdb;

        return $this;
    }

    public function getCountOther(): ?int
    {
        return $this->countOther;
    }

    public function setCountOther(int $countOther): self
    {
        $this->countOther = $countOther;

        return $this;
    }

    public function getCountTime(): ?int
    {
        return $this->countTime;
    }

    public function setCountTime(int $countTime): self
    {
        $this->countTime = $countTime;

        return $this;
    }

    public function getCountSignals(): ?int
    {
        return $this->countSignals;
    }

    public function setCountSignals(int $countSignals): self
    {
        $this->countSignals = $countSignals;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(string $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getGsq(): ?string
    {
        return $this->gsq;
    }

    public function setGsq(string $gsq): self
    {
        $this->gsq = $gsq;

        return $this;
    }

    public function getItu(): ?string
    {
        return $this->itu;
    }

    public function setItu(string $itu): self
    {
        $this->itu = $itu;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLogFormat(): ?string
    {
        return $this->logFormat;
    }

    public function setLogFormat(string $logFormat): self
    {
        $this->logFormat = $logFormat;

        return $this;
    }

    public function getLogLatest(): ?\DateTimeInterface
    {
        return $this->logLatest;
    }

    public function setLogLatest(\DateTimeInterface $logLatest): self
    {
        $this->logLatest = $logLatest;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getMapX(): ?int
    {
        return $this->mapX;
    }

    public function setMapX(int $mapX): self
    {
        $this->mapX = $mapX;

        return $this;
    }

    public function getMapY(): ?int
    {
        return $this->mapY;
    }

    public function setMapY(int $mapY): self
    {
        $this->mapY = $mapY;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPrimaryQth(): ?bool
    {
        return $this->primaryQth;
    }

    public function setPrimaryQth(bool $primaryQth): self
    {
        $this->primaryQth = $primaryQth;

        return $this;
    }

    public function getQth(): ?string
    {
        return $this->qth;
    }

    public function setQth(string $qth): self
    {
        $this->qth = $qth;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getSp(): ?string
    {
        return $this->sp;
    }

    public function setSp(string $sp): self
    {
        $this->sp = $sp;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }


}
