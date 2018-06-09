<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Icao
 *
 * @ORM\Table(name="icao", indexes={@ORM\Index(name="ICAO", columns={"ICAO"})})
 * @ORM\Entity
 */
class Icao
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
     * @var string|null
     *
     * @ORM\Column(name="CNT", type="string", length=2, nullable=true)
     */
    private $cnt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="elevation", type="smallint", nullable=true)
     */
    private $elevation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GSQ", type="string", length=6, nullable=true)
     */
    private $gsq;

    /**
     * @var string
     *
     * @ORM\Column(name="ICAO", type="string", length=4, nullable=false)
     */
    private $icao = '';

    /**
     * @var float|null
     *
     * @ORM\Column(name="lat", type="float", precision=10, scale=0, nullable=true)
     */
    private $lat;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lon", type="float", precision=10, scale=0, nullable=true)
     */
    private $lon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="SP", type="string", length=2, nullable=true)
     */
    private $sp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCnt(): ?string
    {
        return $this->cnt;
    }

    public function setCnt(?string $cnt): self
    {
        $this->cnt = $cnt;

        return $this;
    }

    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    public function setElevation(?int $elevation): self
    {
        $this->elevation = $elevation;

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

    public function getIcao(): ?string
    {
        return $this->icao;
    }

    public function setIcao(string $icao): self
    {
        $this->icao = $icao;

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

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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


}
