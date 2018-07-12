<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Places
 *
 * @ORM\Table(name="places")
 * @ORM\Entity
 */
class Places
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
     * @var bool
     *
     * @ORM\Column(name="capital", type="boolean", nullable=false)
     */
    private $capital = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="itu", type="string", length=3, nullable=false)
     */
    private $itu = '';

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=7, nullable=false)
     */
    private $lat = '';

    /**
     * @var string
     *
     * @ORM\Column(name="lon", type="string", length=8, nullable=false)
     */
    private $lon = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name = '';

    /**
     * @var int
     *
     * @ORM\Column(name="population", type="integer", nullable=false)
     */
    private $population = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="sp", type="string", length=2, nullable=false)
     */
    private $sp = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapital(): ?bool
    {
        return $this->capital;
    }

    public function setCapital(bool $capital): self
    {
        $this->capital = $capital;

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

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?string
    {
        return $this->lon;
    }

    public function setLon(string $lon): self
    {
        $this->lon = $lon;

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

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(int $population): self
    {
        $this->population = $population;

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
}
