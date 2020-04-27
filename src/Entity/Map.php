<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maps
 *
 * @ORM\Table(name="maps")
 * @ORM\Entity
 */
class Map
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
     * @ORM\Column(name="SP", type="string", length=2, nullable=false, options={"fixed"=true})
     */
    private $sp = '';

    /**
     * @var int
     *
     * @ORM\Column(name="ix1", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ix1 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="ix2", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $ix2 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="iy1", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $iy1 = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="iy2", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $iy2 = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="lon1", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lon1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lon2", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lon2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat1", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lat1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat2", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $lat2;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ITU", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $itu = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSp(): ?string
    {
        return $this->sp;
    }

    public function getIx1(): ?int
    {
        return $this->ix1;
    }

    public function setIx1(int $ix1): self
    {
        $this->ix1 = $ix1;

        return $this;
    }

    public function getIx2(): ?int
    {
        return $this->ix2;
    }

    public function setIx2(int $ix2): self
    {
        $this->ix2 = $ix2;

        return $this;
    }

    public function getIy1(): ?int
    {
        return $this->iy1;
    }

    public function setIy1(int $iy1): self
    {
        $this->iy1 = $iy1;

        return $this;
    }

    public function getIy2(): ?int
    {
        return $this->iy2;
    }

    public function setIy2(int $iy2): self
    {
        $this->iy2 = $iy2;

        return $this;
    }

    public function getLon1()
    {
        return $this->lon1;
    }

    public function setLon1($lon1): self
    {
        $this->lon1 = $lon1;

        return $this;
    }

    public function getLon2()
    {
        return $this->lon2;
    }

    public function setLon2($lon2): self
    {
        $this->lon2 = $lon2;

        return $this;
    }

    public function getLat1()
    {
        return $this->lat1;
    }

    public function setLat1($lat1): self
    {
        $this->lat1 = $lat1;

        return $this;
    }

    public function getLat2()
    {
        return $this->lat2;
    }

    public function setLat2($lat2): self
    {
        $this->lat2 = $lat2;

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

    public function getItu(): ?string
    {
        return $this->itu;
    }

    public function setItu(string $itu): self
    {
        $this->itu = $itu;

        return $this;
    }
}
