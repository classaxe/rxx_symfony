<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PollAnswer
 *
 * @ORM\Table(name="poll_answer")
 * @ORM\Entity
 */
class PollAnswer
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
     * @ORM\Column(name="questionID", type="integer", nullable=false)
     */
    private $questionid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=50, nullable=false)
     */
    private $text = '';

    /**
     * @var int
     *
     * @ORM\Column(name="votes", type="integer", nullable=false)
     */
    private $votes = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionid(): ?int
    {
        return $this->questionid;
    }

    public function setQuestionid(int $questionid): self
    {
        $this->questionid = $questionid;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }


}
