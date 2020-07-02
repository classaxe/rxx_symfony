<?php

namespace App\Entity;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Listeners
 *
 * @ORM\Table(name="listeners",indexes={
 *     @ORM\Index(name="count_logs", columns={"count_logs"}),
 *     @ORM\Index(name="name", columns={"name"}),
 *     @ORM\Index(name="QTH", columns={"QTH"}),
 *     @ORM\Index(name="primary_QTH", columns={"primary_QTH"}),
 *     @ORM\Index(name="region", columns={"region"}),
 *     @ORM\Index(name="SP", columns={"SP"}),
 *     @ORM\Index(name="ITU", columns={"ITU"}),
 *     @ORM\Index(name="map_x", columns={"map_x"}),
 *     @ORM\Index(name="map_y", columns={"map_y"})
 * * })
 * @ORM\Entity
 */
class Listener
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
    public $lat = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="log_earliest", type="date", nullable=true)
     */
    private $logEarliest = null;
    /**
     * @var string
     *
     * @ORM\Column(name="log_format", type="string", length=255, nullable=false)
     */
    private $logFormat = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="log_latest", type="date", nullable=true)
     */
    private $logLatest = null;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", precision=10, scale=0, nullable=false)
     */
    public $lon = '0';

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
    private $timezone = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="text", length=65535, nullable=false)
     */
    private $website;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getCallsign(): ?string
    {
        return $this->callsign;
    }

    /**
     * @param string $callsign
     * @return Listener
     */
    public function setCallsign(?string $callsign): self
    {
        $this->callsign = $callsign;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountDgps(): ?int
    {
        return $this->countDgps ? $this->countDgps : null;
    }

    /**
     * @param int $countDgps
     * @return Listener
     */
    public function setCountDgps(int $countDgps): self
    {
        $this->countDgps = $countDgps;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountDsc(): ?int
    {
        return $this->countDsc ? $this->countDsc : null;
    }

    /**
     * @param int $countDsc
     * @return Listener
     */
    public function setCountDsc(int $countDsc): self
    {
        $this->countDsc = $countDsc;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountHambcn(): ?int
    {
        return $this->countHambcn ? $this->countHambcn : null;
    }

    /**
     * @param int $countHambcn
     * @return Listener
     */
    public function setCountHambcn(int $countHambcn): self
    {
        $this->countHambcn = $countHambcn;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountLogs(): ?int
    {
        return $this->countLogs ? $this->countLogs : null;
    }

    /**
     * @param int $countLogs
     * @return Listener
     */
    public function setCountLogs(int $countLogs): self
    {
        $this->countLogs = $countLogs;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountNavtex(): ?int
    {
        return $this->countNavtex ? $this->countNavtex : null;
    }

    /**
     * @param int $countNavtex
     * @return Listener
     */
    public function setCountNavtex(int $countNavtex): self
    {
        $this->countNavtex = $countNavtex;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountNdb(): ?int
    {
        return $this->countNdb ? $this->countNdb : null;
    }

    /**
     * @param int $countNdb
     * @return Listener
     */
    public function setCountNdb(int $countNdb): self
    {
        $this->countNdb = $countNdb;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountOther(): ?int
    {
        return $this->countOther ? $this->countOther : null;
    }

    /**
     * @param int $countOther
     * @return Listener
     */
    public function setCountOther(int $countOther): self
    {
        $this->countOther = $countOther;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountTime(): ?int
    {
        return $this->countTime ? $this->countTime : null;
    }

    /**
     * @param int $countTime
     * @return Listener
     */
    public function setCountTime(int $countTime): self
    {
        $this->countTime = $countTime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountSignals(): ?int
    {
        return $this->countSignals ? $this->countSignals : null;
    }

    /**
     * @param int $countSignals
     * @return Listener
     */
    public function setCountSignals(int $countSignals): self
    {
        $this->countSignals = $countSignals;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Listener
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    /**
     * @param string $equipment
     * @return Listener
     */
    public function setEquipment(?string $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGsq(): ?string
    {
        return $this->gsq;
    }

    /**
     * @param string $gsq
     * @return Listener
     */
    public function setGsq(?string $gsq): self
    {
        $this->gsq = $gsq;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getItu(): ?string
    {
        return $this->itu;
    }

    /**
     * @param string $itu
     * @return Listener
     */
    public function setItu(?string $itu): self
    {
        $this->itu = $itu;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     * @return Listener
     */
    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLogEarliest(): ?DateTimeInterface
    {
        return $this->logEarliest;
    }

    /**
     * @param DateTimeInterface $logEarliest
     * @return Listener
     */
    public function setLogEarliest(DateTimeInterface $logEarliest): self
    {
        $this->logEarliest = $logEarliest;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLogFormat(): ?string
    {
        return $this->logFormat;
    }

    /**
     * @param string $logFormat
     * @return Listener
     */
    public function setLogFormat(?string $logFormat): self
    {
        $this->logFormat = $logFormat;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLogLatest(): ?DateTimeInterface
    {
        return $this->logLatest;
    }

    /**
     * @param DateTimeInterface $logLatest|null
     * @return Listener
     */
    public function setLogLatest(?DateTimeInterface $logLatest): self
    {
        $this->logLatest = $logLatest;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLon(): ?float
    {
        return $this->lon;
    }

    /**
     * @param float $lon
     * @return Listener
     */
    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMapX(): ?int
    {
        return $this->mapX;
    }

    /**
     * @param int $mapX
     * @return Listener
     */
    public function setMapX(?int $mapX): self
    {
        $this->mapX = $mapX;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMapY(): ?int
    {
        return $this->mapY;
    }

    /**
     * @param int $mapY
     * @return Listener
     */
    public function setMapY(?int $mapY): self
    {
        $this->mapY = $mapY;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Listener
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Listener
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPrimaryQth(): ?bool
    {
        return $this->primaryQth;
    }

    /**
     * @param bool $primaryQth
     * @return Listener
     */
    public function setPrimaryQth(?bool $primaryQth): self
    {
        $this->primaryQth = $primaryQth;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getQth(): ?string
    {
        return $this->qth;
    }

    /**
     * @param string $qth
     * @return Listener
     */
    public function setQth(?string $qth): self
    {
        $this->qth = $qth;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @return Listener
     */
    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSp(): ?string
    {
        return $this->sp;
    }

    /**
     * @param string $sp
     * @return Listener
     */
    public function setSp(?string $sp): self
    {
        $this->sp = $sp;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return Listener
     */
    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return Listener
     */
    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddlog(): ?int
    {
        return $this->id;
    }

    /* Custom getters for column display */

    /**
     * @return null|string
     */
    public function getFormattedLogEarliest(): ?string
    {
        if (null === $this->logEarliest || $this->logEarliest->format("Y-m-d") < '1900-01-01') {
            return '';
        }
        return $this->logEarliest->format("Y-m-d");
    }

    /**
     * @return null|string
     */
    public function getFormattedLogLatest(): ?string
    {
        if (null === $this->logLatest || $this->logLatest->format("Y-m-d") < '1900-01-01') {
            return '';
        }
        return $this->logLatest->format("Y-m-d");
    }

    /**
     * @return null|string
     */
    public function getFormattedMapPos(): ?string
    {
        return $this->mapX.','.$this->mapY;
    }

    /**
     * @return null|string
     */
    public function getFormattedNameAndLocation(): ?string
    {
        return
            $this->name . ', '
            . $this->qth.($this->sp ? ', '.$this->sp : '') . ', '
            . $this->itu
            . ' ['.$this->gsq.']';
    }


    /**
     * @return null|bool
     */
    public function getNdbWebLog(): ?bool
    {
        return $this->countLogs;
    }

    /**
     * @return null|bool
     */
    public function getSignalsMap(): ?bool
    {
        return $this->countLogs && ($this->lat || $this->lon);
    }

    public function getDelete(): int
    {
        return $this->id;
    }

    public function isDaytime($hhmm)
    {
        if (!is_numeric($hhmm)) {
            return false;
        }
        return
            $hhmm + 2400 >= ($this->timezone * -100) + 3400 &&
            $hhmm + 2400 <  ($this->timezone * -100) + 3800;
    }

}
