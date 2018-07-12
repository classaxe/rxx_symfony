<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Itu
 *
 * @ORM\Table(name="itu", indexes={
 *     @ORM\Index(name="ITU", columns={"ITU"}),
 *     @ORM\Index(name="region", columns={"region"})
 * })
 * @ORM\Entity
 */
class Itu
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
     * @ORM\Column(name="ITU", type="string", length=3, nullable=false)
     */
    private $itu = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=3, nullable=false)
     */
    private $region = '';

    /**
     * @var string
     *
     * @ORM\Column(name="hasSp", type="integer", nullable=false)
     */
    private $hasSp;

    /**
     * @var string
     *
     * @ORM\Column(name="spTitle", length=45, nullable=false)
     */
    private $spTitle = '';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getHasSp(): ?int
    {
        return $this->hasSp;
    }

    public function setHasSp(int $hasSp): self
    {
        $this->hasSp = $hasSp;

        return $this;
    }

    public function getSpTitle(): ?string
    {
        return $this->spTitle;
    }

    public function setSpTitle(string $spTitle): self
    {
        $this->spTitle = $spTitle;

        return $this;
    }
}
