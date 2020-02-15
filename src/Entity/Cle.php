<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cle
 *
 * @ORM\Table(name="cle")
 * @ORM\Entity
 */
class Cle
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cle", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $cle = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_start", type="date", nullable=true, options={"default"="0000-00-00"})
     */
    private $dateStart = '0000-00-00';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_end", type="date", nullable=true, options={"default"="0000-00-00"})
     */
    private $dateEnd = '0000-00-00';

    /**
     * @var string|null
     *
     * @ORM\Column(name="date_timespan", type="string", length=255, nullable=true)
     */
    private $dateTimespan = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="scope", type="string", length=255, nullable=true)
     */
    private $scope = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="additional", type="string", length=255, nullable=true)
     */
    private $additional = '';

    /**
     * @var float|null
     *
     * @ORM\Column(name="world_range_1_low", type="float", precision=10, scale=0, nullable=true)
     */
    private $worldRange1Low = '0';

    /**
     * @var float|null
     *
     * @ORM\Column(name="world_range_1_high", type="float", precision=10, scale=0, nullable=true)
     */
    private $worldRange1High = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_channels", type="string", length=0, nullable=true)
     */
    private $worldRange1Channels;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_type", type="text", length=65535, nullable=true)
     */
    private $worldRange1Type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_locator", type="text", length=65535, nullable=true)
     */
    private $worldRange1Locator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_itu", type="text", length=65535, nullable=true)
     */
    private $worldRange1Itu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_sp", type="text", length=65535, nullable=true)
     */
    private $worldRange1Sp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_sp_itu_clause", type="string", length=0, nullable=true)
     */
    private $worldRange1SpItuClause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_filter_other", type="text", length=65535, nullable=true)
     */
    private $worldRange1FilterOther;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_1_text_extra", type="text", length=65535, nullable=true)
     */
    private $worldRange1TextExtra;

    /**
     * @var float|null
     *
     * @ORM\Column(name="world_range_2_low", type="float", precision=10, scale=0, nullable=true)
     */
    private $worldRange2Low = '0';

    /**
     * @var float|null
     *
     * @ORM\Column(name="world_range_2_high", type="float", precision=10, scale=0, nullable=true)
     */
    private $worldRange2High = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_channels", type="string", length=0, nullable=true)
     */
    private $worldRange2Channels;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_type", type="text", length=65535, nullable=true)
     */
    private $worldRange2Type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_locator", type="text", length=65535, nullable=true)
     */
    private $worldRange2Locator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_itu", type="text", length=65535, nullable=true)
     */
    private $worldRange2Itu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_sp", type="text", length=65535, nullable=true)
     */
    private $worldRange2Sp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_sp_itu_clause", type="string", length=0, nullable=true)
     */
    private $worldRange2SpItuClause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_filter_other", type="text", length=65535, nullable=true)
     */
    private $worldRange2FilterOther;

    /**
     * @var string|null
     *
     * @ORM\Column(name="world_range_2_text_extra", type="text", length=65535, nullable=true)
     */
    private $worldRange2TextExtra;

    /**
     * @var float|null
     *
     * @ORM\Column(name="europe_range_1_low", type="float", precision=10, scale=0, nullable=true)
     */
    private $europeRange1Low = '0';

    /**
     * @var float|null
     *
     * @ORM\Column(name="europe_range_1_high", type="float", precision=10, scale=0, nullable=true)
     */
    private $europeRange1High = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_channels", type="string", length=0, nullable=true)
     */
    private $europeRange1Channels;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_type", type="text", length=65535, nullable=true)
     */
    private $europeRange1Type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_locator", type="text", length=65535, nullable=true)
     */
    private $europeRange1Locator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_itu", type="text", length=65535, nullable=true)
     */
    private $europeRange1Itu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_sp", type="text", length=65535, nullable=true)
     */
    private $europeRange1Sp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_sp_itu_clause", type="string", length=0, nullable=true)
     */
    private $europeRange1SpItuClause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_filter_other", type="text", length=65535, nullable=true)
     */
    private $europeRange1FilterOther;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_1_text_extra", type="text", length=65535, nullable=true)
     */
    private $europeRange1TextExtra;

    /**
     * @var float|null
     *
     * @ORM\Column(name="europe_range_2_low", type="float", precision=10, scale=0, nullable=true)
     */
    private $europeRange2Low = '0';

    /**
     * @var float|null
     *
     * @ORM\Column(name="europe_range_2_high", type="float", precision=10, scale=0, nullable=true)
     */
    private $europeRange2High = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_channels", type="string", length=0, nullable=true)
     */
    private $europeRange2Channels;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_type", type="text", length=65535, nullable=true)
     */
    private $europeRange2Type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_locator", type="text", length=65535, nullable=true)
     */
    private $europeRange2Locator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_itu", type="text", length=65535, nullable=true)
     */
    private $europeRange2Itu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_sp", type="text", length=65535, nullable=true)
     */
    private $europeRange2Sp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_sp_itu_clause", type="string", length=0, nullable=true)
     */
    private $europeRange2SpItuClause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_filter_other", type="text", length=65535, nullable=true)
     */
    private $europeRange2FilterOther;

    /**
     * @var string|null
     *
     * @ORM\Column(name="europe_range_2_text_extra", type="text", length=65535, nullable=true)
     */
    private $europeRange2TextExtra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCle(): ?int
    {
        return $this->cle;
    }

    public function setCle(?int $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    public function getDateStart(): ?DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateTimespan(): ?string
    {
        return $this->dateTimespan;
    }

    public function setDateTimespan(?string $dateTimespan): self
    {
        $this->dateTimespan = $dateTimespan;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(?string $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function getWorldRange1Low(): ?float
    {
        return $this->worldRange1Low;
    }

    public function setWorldRange1Low(?float $worldRange1Low): self
    {
        $this->worldRange1Low = $worldRange1Low;

        return $this;
    }

    public function getWorldRange1High(): ?float
    {
        return $this->worldRange1High;
    }

    public function setWorldRange1High(?float $worldRange1High): self
    {
        $this->worldRange1High = $worldRange1High;

        return $this;
    }

    public function getWorldRange1Channels(): ?string
    {
        return $this->worldRange1Channels;
    }

    public function setWorldRange1Channels(?string $worldRange1Channels): self
    {
        $this->worldRange1Channels = $worldRange1Channels;

        return $this;
    }

    public function getWorldRange1Type(): ?string
    {
        return $this->worldRange1Type;
    }

    public function setWorldRange1Type(?string $worldRange1Type): self
    {
        $this->worldRange1Type = $worldRange1Type;

        return $this;
    }

    public function getWorldRange1Locator(): ?string
    {
        return $this->worldRange1Locator;
    }

    public function setWorldRange1Locator(?string $worldRange1Locator): self
    {
        $this->worldRange1Locator = $worldRange1Locator;

        return $this;
    }

    public function getWorldRange1Itu(): ?string
    {
        return $this->worldRange1Itu;
    }

    public function setWorldRange1Itu(?string $worldRange1Itu): self
    {
        $this->worldRange1Itu = $worldRange1Itu;

        return $this;
    }

    public function getWorldRange1Sp(): ?string
    {
        return $this->worldRange1Sp;
    }

    public function setWorldRange1Sp(?string $worldRange1Sp): self
    {
        $this->worldRange1Sp = $worldRange1Sp;

        return $this;
    }

    public function getWorldRange1SpItuClause(): ?string
    {
        return $this->worldRange1SpItuClause;
    }

    public function setWorldRange1SpItuClause(?string $worldRange1SpItuClause): self
    {
        $this->worldRange1SpItuClause = $worldRange1SpItuClause;

        return $this;
    }

    public function getWorldRange1FilterOther(): ?string
    {
        return $this->worldRange1FilterOther;
    }

    public function setWorldRange1FilterOther(?string $worldRange1FilterOther): self
    {
        $this->worldRange1FilterOther = $worldRange1FilterOther;

        return $this;
    }

    public function getWorldRange1TextExtra(): ?string
    {
        return $this->worldRange1TextExtra;
    }

    public function setWorldRange1TextExtra(?string $worldRange1TextExtra): self
    {
        $this->worldRange1TextExtra = $worldRange1TextExtra;

        return $this;
    }

    public function getWorldRange2Low(): ?float
    {
        return $this->worldRange2Low;
    }

    public function setWorldRange2Low(?float $worldRange2Low): self
    {
        $this->worldRange2Low = $worldRange2Low;

        return $this;
    }

    public function getWorldRange2High(): ?float
    {
        return $this->worldRange2High;
    }

    public function setWorldRange2High(?float $worldRange2High): self
    {
        $this->worldRange2High = $worldRange2High;

        return $this;
    }

    public function getWorldRange2Channels(): ?string
    {
        return $this->worldRange2Channels;
    }

    public function setWorldRange2Channels(?string $worldRange2Channels): self
    {
        $this->worldRange2Channels = $worldRange2Channels;

        return $this;
    }

    public function getWorldRange2Type(): ?string
    {
        return $this->worldRange2Type;
    }

    public function setWorldRange2Type(?string $worldRange2Type): self
    {
        $this->worldRange2Type = $worldRange2Type;

        return $this;
    }

    public function getWorldRange2Locator(): ?string
    {
        return $this->worldRange2Locator;
    }

    public function setWorldRange2Locator(?string $worldRange2Locator): self
    {
        $this->worldRange2Locator = $worldRange2Locator;

        return $this;
    }

    public function getWorldRange2Itu(): ?string
    {
        return $this->worldRange2Itu;
    }

    public function setWorldRange2Itu(?string $worldRange2Itu): self
    {
        $this->worldRange2Itu = $worldRange2Itu;

        return $this;
    }

    public function getWorldRange2Sp(): ?string
    {
        return $this->worldRange2Sp;
    }

    public function setWorldRange2Sp(?string $worldRange2Sp): self
    {
        $this->worldRange2Sp = $worldRange2Sp;

        return $this;
    }

    public function getWorldRange2SpItuClause(): ?string
    {
        return $this->worldRange2SpItuClause;
    }

    public function setWorldRange2SpItuClause(?string $worldRange2SpItuClause): self
    {
        $this->worldRange2SpItuClause = $worldRange2SpItuClause;

        return $this;
    }

    public function getWorldRange2FilterOther(): ?string
    {
        return $this->worldRange2FilterOther;
    }

    public function setWorldRange2FilterOther(?string $worldRange2FilterOther): self
    {
        $this->worldRange2FilterOther = $worldRange2FilterOther;

        return $this;
    }

    public function getWorldRange2TextExtra(): ?string
    {
        return $this->worldRange2TextExtra;
    }

    public function setWorldRange2TextExtra(?string $worldRange2TextExtra): self
    {
        $this->worldRange2TextExtra = $worldRange2TextExtra;

        return $this;
    }

    public function getEuropeRange1Low(): ?float
    {
        return $this->europeRange1Low;
    }

    public function setEuropeRange1Low(?float $europeRange1Low): self
    {
        $this->europeRange1Low = $europeRange1Low;

        return $this;
    }

    public function getEuropeRange1High(): ?float
    {
        return $this->europeRange1High;
    }

    public function setEuropeRange1High(?float $europeRange1High): self
    {
        $this->europeRange1High = $europeRange1High;

        return $this;
    }

    public function getEuropeRange1Channels(): ?string
    {
        return $this->europeRange1Channels;
    }

    public function setEuropeRange1Channels(?string $europeRange1Channels): self
    {
        $this->europeRange1Channels = $europeRange1Channels;

        return $this;
    }

    public function getEuropeRange1Type(): ?string
    {
        return $this->europeRange1Type;
    }

    public function setEuropeRange1Type(?string $europeRange1Type): self
    {
        $this->europeRange1Type = $europeRange1Type;

        return $this;
    }

    public function getEuropeRange1Locator(): ?string
    {
        return $this->europeRange1Locator;
    }

    public function setEuropeRange1Locator(?string $europeRange1Locator): self
    {
        $this->europeRange1Locator = $europeRange1Locator;

        return $this;
    }

    public function getEuropeRange1Itu(): ?string
    {
        return $this->europeRange1Itu;
    }

    public function setEuropeRange1Itu(?string $europeRange1Itu): self
    {
        $this->europeRange1Itu = $europeRange1Itu;

        return $this;
    }

    public function getEuropeRange1Sp(): ?string
    {
        return $this->europeRange1Sp;
    }

    public function setEuropeRange1Sp(?string $europeRange1Sp): self
    {
        $this->europeRange1Sp = $europeRange1Sp;

        return $this;
    }

    public function getEuropeRange1SpItuClause(): ?string
    {
        return $this->europeRange1SpItuClause;
    }

    public function setEuropeRange1SpItuClause(?string $europeRange1SpItuClause): self
    {
        $this->europeRange1SpItuClause = $europeRange1SpItuClause;

        return $this;
    }

    public function getEuropeRange1FilterOther(): ?string
    {
        return $this->europeRange1FilterOther;
    }

    public function setEuropeRange1FilterOther(?string $europeRange1FilterOther): self
    {
        $this->europeRange1FilterOther = $europeRange1FilterOther;

        return $this;
    }

    public function getEuropeRange1TextExtra(): ?string
    {
        return $this->europeRange1TextExtra;
    }

    public function setEuropeRange1TextExtra(?string $europeRange1TextExtra): self
    {
        $this->europeRange1TextExtra = $europeRange1TextExtra;

        return $this;
    }

    public function getEuropeRange2Low(): ?float
    {
        return $this->europeRange2Low;
    }

    public function setEuropeRange2Low(?float $europeRange2Low): self
    {
        $this->europeRange2Low = $europeRange2Low;

        return $this;
    }

    public function getEuropeRange2High(): ?float
    {
        return $this->europeRange2High;
    }

    public function setEuropeRange2High(?float $europeRange2High): self
    {
        $this->europeRange2High = $europeRange2High;

        return $this;
    }

    public function getEuropeRange2Channels(): ?string
    {
        return $this->europeRange2Channels;
    }

    public function setEuropeRange2Channels(?string $europeRange2Channels): self
    {
        $this->europeRange2Channels = $europeRange2Channels;

        return $this;
    }

    public function getEuropeRange2Type(): ?string
    {
        return $this->europeRange2Type;
    }

    public function setEuropeRange2Type(?string $europeRange2Type): self
    {
        $this->europeRange2Type = $europeRange2Type;

        return $this;
    }

    public function getEuropeRange2Locator(): ?string
    {
        return $this->europeRange2Locator;
    }

    public function setEuropeRange2Locator(?string $europeRange2Locator): self
    {
        $this->europeRange2Locator = $europeRange2Locator;

        return $this;
    }

    public function getEuropeRange2Itu(): ?string
    {
        return $this->europeRange2Itu;
    }

    public function setEuropeRange2Itu(?string $europeRange2Itu): self
    {
        $this->europeRange2Itu = $europeRange2Itu;

        return $this;
    }

    public function getEuropeRange2Sp(): ?string
    {
        return $this->europeRange2Sp;
    }

    public function setEuropeRange2Sp(?string $europeRange2Sp): self
    {
        $this->europeRange2Sp = $europeRange2Sp;

        return $this;
    }

    public function getEuropeRange2SpItuClause(): ?string
    {
        return $this->europeRange2SpItuClause;
    }

    public function setEuropeRange2SpItuClause(?string $europeRange2SpItuClause): self
    {
        $this->europeRange2SpItuClause = $europeRange2SpItuClause;

        return $this;
    }

    public function getEuropeRange2FilterOther(): ?string
    {
        return $this->europeRange2FilterOther;
    }

    public function setEuropeRange2FilterOther(?string $europeRange2FilterOther): self
    {
        $this->europeRange2FilterOther = $europeRange2FilterOther;

        return $this;
    }

    public function getEuropeRange2TextExtra(): ?string
    {
        return $this->europeRange2TextExtra;
    }

    public function setEuropeRange2TextExtra(?string $europeRange2TextExtra): self
    {
        $this->europeRange2TextExtra = $europeRange2TextExtra;

        return $this;
    }
}
