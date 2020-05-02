<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $temp_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BigGame", cascade={"persist"}, inversedBy="game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bigGame;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="games")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, unique=false)
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTempId(): ?int
    {
        return $this->temp_id;
    }

    public function setTempId(?int $temp_id): self
    {
        $this->temp_id = $temp_id;

        return $this;
    }

    public function getAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(bool $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getBigGame(): ?BigGame
    {
        return $this->bigGame;
    }

    public function setBigGame(?BigGame $bigGame): self
    {
        $this->bigGame = $bigGame;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
