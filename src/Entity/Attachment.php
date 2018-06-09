<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attachment
 *
 * @ORM\Table(name="attachment", indexes={@ORM\Index(name="destinationID", columns={"destinationID"}), @ORM\Index(name="destinationTable", columns={"destinationTable"})})
 * @ORM\Entity
 */
class Attachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="blob", length=0, nullable=false)
     */
    private $data;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="destinationID", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $destinationid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="destinationTable", type="string", length=20, nullable=false)
     */
    private $destinationtable = '';

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $size = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     */
    private $type = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="historyAddedDate", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $historyaddeddate = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="historyModifiedDate", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $historymodifieddate = '0000-00-00 00:00:00';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDestinationid(): ?int
    {
        return $this->destinationid;
    }

    public function setDestinationid(int $destinationid): self
    {
        $this->destinationid = $destinationid;

        return $this;
    }

    public function getDestinationtable(): ?string
    {
        return $this->destinationtable;
    }

    public function setDestinationtable(string $destinationtable): self
    {
        $this->destinationtable = $destinationtable;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getHistoryaddeddate(): ?\DateTimeInterface
    {
        return $this->historyaddeddate;
    }

    public function setHistoryaddeddate(\DateTimeInterface $historyaddeddate): self
    {
        $this->historyaddeddate = $historyaddeddate;

        return $this;
    }

    public function getHistorymodifieddate(): ?\DateTimeInterface
    {
        return $this->historymodifieddate;
    }

    public function setHistorymodifieddate(\DateTimeInterface $historymodifieddate): self
    {
        $this->historymodifieddate = $historymodifieddate;

        return $this;
    }


}
