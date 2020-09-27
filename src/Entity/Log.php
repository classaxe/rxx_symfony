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
 *          @ORM\Index(name="idx_dx_km", columns={"dx_km"}),
 *          @ORM\Index(name="idx_dx_miles", columns={"dx_miles"}),
 *          @ORM\Index(name="idx_heard_in", columns={"heard_in"}),
 *          @ORM\Index(name="idx_listenerID", columns={"listenerID"}),
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
    private $signalid = '0';

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
     * @var int|null
     *
     * @ORM\Column(name="listenerID", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $listenerid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="LSB", type="smallint", nullable=true)
     */
    private $lsb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="LSB_approx", type="string", length=1, nullable=true, options={"fixed"=true})
     */
    private $lsbApprox;

    /**
     * @var string
     *
     * @ORM\Column(name="heard_in", type="string", length=3, nullable=false)
     */
    private $heardIn = '';

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
     * @var string|null
     *
     * @ORM\Column(name="USB_approx", type="string", length=1, nullable=true, options={"fixed"=true})
     */
    private $usbApprox;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSignalId(): ?int
    {
        return $this->signalid;
    }

    public function setSignalId(int $signalid): self
    {
        $this->signalid = $signalid;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDaytime(): ?bool
    {
        return $this->daytime;
    }

    public function setDaytime(bool $daytime): self
    {
        $this->daytime = $daytime;

        return $this;
    }

    public function getDxKm(): ?int
    {
        return $this->dxKm;
    }

    public function setDxKm(?int $dxKm): self
    {
        $this->dxKm = $dxKm;

        return $this;
    }

    public function getDxMiles(): ?int
    {
        return $this->dxMiles;
    }

    public function setDxMiles(?int $dxMiles): self
    {
        $this->dxMiles = $dxMiles;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getListenerId(): ?int
    {
        return $this->listenerid;
    }

    public function setListenerId(?int $listenerid): self
    {
        $this->listenerid = $listenerid;

        return $this;
    }

    public function getLsb(): ?int
    {
        return $this->lsb;
    }

    public function setLsb(?int $lsb): self
    {
        $this->lsb = $lsb;

        return $this;
    }

    public function getLsbApprox(): ?string
    {
        return $this->lsbApprox;
    }

    public function setLsbApprox(?string $lsbApprox): self
    {
        $this->lsbApprox = $lsbApprox;

        return $this;
    }

    public function getHeardIn(): ?string
    {
        return $this->heardIn;
    }

    public function setHeardIn(string $heardIn): self
    {
        $this->heardIn = $heardIn;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getSec(): ?string
    {
        return $this->sec;
    }

    public function setSec(string $sec): self
    {
        $this->sec = $sec;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getUsb(): ?int
    {
        return $this->usb;
    }

    public function setUsb(?int $usb): self
    {
        $this->usb = $usb;

        return $this;
    }

    public function getUsbApprox(): ?string
    {
        return $this->usbApprox;
    }

    public function setUsbApprox(?string $usbApprox): self
    {
        $this->usbApprox = $usbApprox;

        return $this;
    }
}
