<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Signals
 *
 * @ORM\Table(
 *     name="signals",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="no_duplicates", columns={"call", "khz", "GSQ", "ITU", "SP"})
 *     },
 *     indexes={
 *          @ORM\Index(name="last_heard", columns={"last_heard"}),
 *          @ORM\Index(name="type", columns={"type"}),
 *          @ORM\Index(name="ID", columns={"ID"}),
 *          @ORM\Index(name="active", columns={"active"}),
 *          @ORM\Index(name="khz", columns={"khz"}),
 *          @ORM\Index(name="SP", columns={"SP"}),
 *          @ORM\Index(name="ITU", columns={"ITU"}),
 *          @ORM\Index(name="logs", columns={"logs"}),
 *          @ORM\Index(name="call", columns={"call"}),
 *          @ORM\Index(name="first_heard", columns={"first_heard"}),
 *          @ORM\Index(name="heard_in_af", columns={"heard_in_af"}),
 *          @ORM\Index(name="heard_in_an", columns={"heard_in_an"}),
 *          @ORM\Index(name="heard_in_as", columns={"heard_in_as"}),
 *          @ORM\Index(name="heard_in_ca", columns={"heard_in_ca"}),
 *          @ORM\Index(name="heard_in_eu", columns={"heard_in_eu"}),
 *          @ORM\Index(name="heard_in_iw", columns={"heard_in_iw"}),
 *          @ORM\Index(name="heard_in_na", columns={"heard_in_na"}),
 *          @ORM\Index(name="heard_in_oc", columns={"heard_in_oc"}),
 *          @ORM\Index(name="heard_in_sa", columns={"heard_in_sa"}),
 *          @ORM\Index(name="heard_in_xx", columns={"heard_in_xx"}),
 *          @ORM\Index(name="region", columns={"region"})
 *     }
 * )
 * @ORM\Entity
 */
class Signal
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
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="call", type="string", length=12, nullable=false)
     */
    private $call = '';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="first_heard", type="date", nullable=true)
     */
    private $firstHeard;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=25, nullable=false)
     */
    private $format = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="GSQ", type="string", length=6, nullable=true)
     */
    private $gsq;

    /**
     * @var string|null
     *
     * @ORM\Column(name="heard_in", type="string", length=255, nullable=true)
     */
    private $heardIn;

    /**
     * @var string
     *
     * @ORM\Column(name="heard_in_html", type="text", length=65535, nullable=false)
     */
    private $heardInHtml;

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_af", type="boolean", nullable=false)
     */
    private $heardInAf = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_an", type="boolean", nullable=false)
     */
    private $heardInAn = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_as", type="boolean", nullable=false)
     */
    private $heardInAs = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_ca", type="boolean", nullable=false)
     */
    private $heardInCa = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_eu", type="boolean", nullable=false)
     */
    private $heardInEu = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_iw", type="boolean", nullable=false)
     */
    private $heardInIw = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_na", type="boolean", nullable=false)
     */
    private $heardInNa = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_oc", type="boolean", nullable=false)
     */
    private $heardInOc = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_sa", type="boolean", nullable=false)
     */
    private $heardInSa = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="heard_in_xx", type="boolean", nullable=false)
     */
    private $heardInXx = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ITU", type="string", length=3, nullable=true)
     */
    private $itu;

    /**
     * @var string
     *
     * @ORM\Column(name="khz", type="decimal", precision=9, scale=3, nullable=false, options={"default"="0.000"})
     */
    private $khz = '0.000';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_heard", type="date", nullable=true)
     */
    private $lastHeard;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lat", type="float", precision=10, scale=0, nullable=true)
     */
    private $lat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="QTH", type="text", length=65535, nullable=true)
     */
    private $qth;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logs", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $logs;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lon", type="float", precision=10, scale=0, nullable=true)
     */
    private $lon;

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
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var float
     *
     * @ORM\Column(name="pwr", type="float", precision=10, scale=0, nullable=false)
     */
    private $pwr = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=2, nullable=false)
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="sec", type="string", length=12, nullable=false)
     */
    private $sec = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="SP", type="string", length=3, nullable=true)
     */
    private $sp;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1, nullable=false, options={"fixed"=true})
     */
    private $type = '';

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

    private $rangeDeg;
    private $rangeKm;
    private $rangeMi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCall(): ?string
    {
        return $this->call;
    }

    public function setCall(string $call): self
    {
        $this->call = $call;

        return $this;
    }

    public function getFirstHeard(): ?\DateTimeInterface
    {
        return $this->firstHeard;
    }

    public function setFirstHeard(?\DateTimeInterface $firstHeard): self
    {
        $this->firstHeard = $firstHeard;

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

    public function getGsq(): ?string
    {
        return $this->gsq;
    }

    public function setGsq(?string $gsq): self
    {
        $this->gsq = $gsq;

        return $this;
    }

    public function getHeardIn(): ?string
    {
        return $this->heardIn;
    }

    public function setHeardIn(?string $heardIn): self
    {
        $this->heardIn = $heardIn;

        return $this;
    }

    public function getHeardInHtml(): ?string
    {
        return str_replace(['<b>','</b>'],['<em>','</em>'],$this->heardInHtml);
    }

    public function setHeardInHtml(string $heardInHtml): self
    {
        $this->heardInHtml = $heardInHtml;

        return $this;
    }

    public function getHeardInAf(): ?bool
    {
        return $this->heardInAf;
    }

    public function setHeardInAf(bool $heardInAf): self
    {
        $this->heardInAf = $heardInAf;

        return $this;
    }

    public function getHeardInAn(): ?bool
    {
        return $this->heardInAn;
    }

    public function setHeardInAn(bool $heardInAn): self
    {
        $this->heardInAn = $heardInAn;

        return $this;
    }

    public function getHeardInAs(): ?bool
    {
        return $this->heardInAs;
    }

    public function setHeardInAs(bool $heardInAs): self
    {
        $this->heardInAs = $heardInAs;

        return $this;
    }

    public function getHeardInCa(): ?bool
    {
        return $this->heardInCa;
    }

    public function setHeardInCa(bool $heardInCa): self
    {
        $this->heardInCa = $heardInCa;

        return $this;
    }

    public function getHeardInEu(): ?bool
    {
        return $this->heardInEu;
    }

    public function setHeardInEu(bool $heardInEu): self
    {
        $this->heardInEu = $heardInEu;

        return $this;
    }

    public function getHeardInIw(): ?bool
    {
        return $this->heardInIw;
    }

    public function setHeardInIw(bool $heardInIw): self
    {
        $this->heardInIw = $heardInIw;

        return $this;
    }

    public function getHeardInNa(): ?bool
    {
        return $this->heardInNa;
    }

    public function setHeardInNa(bool $heardInNa): self
    {
        $this->heardInNa = $heardInNa;

        return $this;
    }

    public function getHeardInOc(): ?bool
    {
        return $this->heardInOc;
    }

    public function setHeardInOc(bool $heardInOc): self
    {
        $this->heardInOc = $heardInOc;

        return $this;
    }

    public function getHeardInSa(): ?bool
    {
        return $this->heardInSa;
    }

    public function setHeardInSa(bool $heardInSa): self
    {
        $this->heardInSa = $heardInSa;

        return $this;
    }

    public function getHeardInXx(): ?bool
    {
        return $this->heardInXx;
    }

    public function setHeardInXx(bool $heardInXx): self
    {
        $this->heardInXx = $heardInXx;

        return $this;
    }

    public function getItu(): ?string
    {
        return $this->itu;
    }

    public function setItu(?string $itu): self
    {
        $this->itu = $itu;

        return $this;
    }

    public function getKhz()
    {
        return (float)$this->khz;
    }

    public function setKhz($khz): self
    {
        $this->khz = $khz;

        return $this;
    }

    public function getLastHeard(): ?\DateTimeInterface
    {
        return $this->lastHeard;
    }

    public function setLastHeard(?\DateTimeInterface $lastHeard): self
    {
        $this->lastHeard = $lastHeard;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getQth(): ?string
    {
        return $this->qth;
    }

    public function setQth(?string $qth): self
    {
        $this->qth = $qth;

        return $this;
    }

    public function getLogs(): ?int
    {
        return $this->logs;
    }

    public function setLogs(?int $logs): self
    {
        $this->logs = $logs;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPwr(): ?float
    {
        return $this->pwr;
    }

    public function setPwr(float $pwr): self
    {
        $this->pwr = $pwr;

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

    public function getSp(): ?string
    {
        return $this->sp;
    }

    public function setSp(?string $sp): self
    {
        $this->sp = $sp;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    /**
     * @return null|string
     */
    public function getFormattedDeleteLink(): ?string
    {
        $url =  "signals/{$this->id}/delete";
        return "<a href=\"{$url}\" onclick=\"return confirm('Delete this Signal?  Are you sure?');\">Delete</a>";
    }

    /**
     * @return null|string
     */
    public function getFormattedFirstHeard(): ?string
    {
        if (is_null($this->firstHeard)) {
            return "";
        }
        if ($this->firstHeard->format("Y-m-d") < '1900-01-01') {
            return '';
        }
        return $this->firstHeard->format("Y-m-d");
    }

    /**
     * @return null|string
     */
    public function getFormattedLastHeard(): ?string
    {
        if (is_null($this->lastHeard)) {
            return '';
        }
        if ($this->lastHeard->format("Y-m-d") < '1900-01-01') {
            return '';
        }
        return $this->lastHeard->format("Y-m-d");
    }

    /**
     * @return null|string
     */
    public function getFormattedLogsLink(): ?string
    {
        $url =  "signals/{$this->id}/logs";
        return "<a href=\"{$url}\">{$this->logs}</a>";
    }

    /**
     * @return null|string
     */
    public function getFormattedRegion(): ?string
    {
        return strtoupper($this->region);
    }

    /**
     * @return null|float
     */
    public function getFormattedPwr(): ?string
    {
        return $this->pwr ?: '';
    }

    /**
     * @return null|float
     */
    public function getFormattedRangeKm(): ?string
    {
        if (!$this->lat && !$this->lon) {
            return '';
        }
        return $this->rangeKm;
    }

    /**
     * @return null|float
     */
    public function getFormattedRangeMi(): ?string
    {
        if (!$this->lat && !$this->lon) {
            return '';
        }
        return $this->rangeMi;
    }
}
