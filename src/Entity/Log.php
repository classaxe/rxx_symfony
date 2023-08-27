<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Logs
 *
 * @ORM\Table(
 *     name="logs",
 *     indexes={
 *          @ORM\Index(name="idx_date", columns={"date"}),
 *          @ORM\Index(name="idx_dx_deg", columns={"dx_deg"}),
 *          @ORM\Index(name="idx_dx_km", columns={"dx_km"}),
 *          @ORM\Index(name="idx_dx_miles", columns={"dx_miles"}),
 *          @ORM\Index(name="idx_heard_in", columns={"heard_in"}),
 *          @ORM\Index(name="idx_listenerID", columns={"listenerID"}),
 *          @ORM\Index(name="idx_logSessionID", columns={"logSessionID"}),
 *          @ORM\Index(name="idx_operatorID", columns={"operatorID"}),
 *          @ORM\Index(name="idx_signalID", columns={"signalID"}),
 *          @ORM\Index(name="idx_daytime", columns={"daytime"}),
 *          @ORM\Index(name="idx_region", columns={"region"})
 *     }
 * )
 * @ORM\Entity
 */
class Log
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
     * @var int
     *
     * @ORM\Column(name="signalID", type="integer", nullable=false)
     */
    private $signalId = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="daytime", type="boolean", nullable=false)
     */
    private $daytime = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="dx_deg", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $dxDeg;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dx_km", type="smallint", nullable=true)
     */
    private $dxKm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dx_miles", type="smallint", nullable=true)
     */
    private $dxMiles;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=25, nullable=false)
     */
    private $format = '';

    /**
     * @var string
     *
     * @ORM\Column(name="heard_in", type="string", length=3, nullable=false)
     */
    private $heardIn = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="listenerID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listenerId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logSessionID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logSessionId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="LSB", type="smallint", nullable=true)
     */
    private $lsb;

    /**
     * @var string
     *
     * @ORM\Column(name="LSB_approx", type="string", length=1, nullable=false, options={"fixed"=true})
     */
    private $lsbApprox;

    /**
     * @var int|null
     *
     * @ORM\Column(name="operatorID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $operatorId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region", type="string", length=2, nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="sec", type="string", length=12, nullable=false)
     */
    private $sec = '';

    /**
     * @var string
     *
     * @ORM\Column(name="time", type="string", length=5, nullable=false)
     */
    private $time = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="USB", type="smallint", nullable=true)
     */
    private $usb;

    /**
     * @var string
     *
     * @ORM\Column(name="USB_approx", type="string", length=1, nullable=false, options={"fixed"=true})
     */
    private $usbApprox;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getSignalId(): ?int
    {
        return $this->signalId;
    }

    /**
     * @param int $signalId
     * @return $this
     */
    public function setSignalId(int $signalId): self
    {
        $this->signalId = $signalId;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface|null $date
     * @return $this
     */
    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDaytime(): ?bool
    {
        return $this->daytime;
    }

    /**
     * @param bool $daytime
     * @return $this
     */
    public function setDaytime(bool $daytime): self
    {
        $this->daytime = $daytime;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDxDeg(): ?int
    {
        return $this->dxDeg;
    }

    /**
     * @param int|null $dxDeg
     * @return $this
     */
    public function setDxDeg(?int $dxDeg): self
    {
        $this->dxDeg = $dxDeg;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDxKm(): ?int
    {
        return $this->dxKm;
    }

    /**
     * @param int|null $dxKm
     * @return $this
     */
    public function setDxKm(?int $dxKm): self
    {
        $this->dxKm = $dxKm;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDxMiles(): ?int
    {
        return $this->dxMiles;
    }

    /**
     * @param int|null $dxMiles
     * @return $this
     */
    public function setDxMiles(?int $dxMiles): self
    {
        $this->dxMiles = $dxMiles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeardIn(): ?string
    {
        return $this->heardIn;
    }

    /**
     * @param string $heardIn
     * @return $this
     */
    public function setHeardIn(string $heardIn): self
    {
        $this->heardIn = $heardIn;

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
     * @param int|null $listenerId
     * @return $this
     */
    public function setListenerId(?int $listenerId): self
    {
        $this->listenerId = $listenerId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLogSessionId(): ?int
    {
        return $this->logSessionId;
    }

    /**
     * @param int|null $logSessionId
     * @return $this
     */
    public function setLogSessionId(?int $logSessionId): self
    {
        $this->logSessionId = $logSessionId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLsb(): ?int
    {
        return $this->lsb;
    }

    /**
     * @param int|null $lsb
     * @return $this
     */
    public function setLsb(?int $lsb): self
    {
        $this->lsb = $lsb;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getLsbApprox(): ?bool
    {
        return $this->lsbApprox !== '';
    }

    /**
     * @param int|null $lsbApprox
     * @return $this
     */
    public function setLsbApprox(?int $lsbApprox): self
    {
        $this->lsbApprox = $lsbApprox ? '~' : '';

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

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return $this
     */
    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSec(): ?string
    {
        return $this->sec;
    }

    /**
     * @param string $sec
     * @return $this
     */
    public function setSec(string $sec): self
    {
        $this->sec = $sec;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @param string $time
     * @return $this
     */
    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUsb(): ?int
    {
        return $this->usb;
    }

    /**
     * @param int|null $usb
     * @return $this
     */
    public function setUsb(?int $usb): self
    {
        $this->usb = $usb;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getUsbApprox(): ?bool
    {
        return $this->usbApprox !== '';
    }

    /**
     * @param bool|null $usbApprox
     * @return $this
     */
    public function setUsbApprox(?bool $usbApprox): self
    {
        $this->usbApprox = $usbApprox ? '~' : '';

        return $this;
    }
}
