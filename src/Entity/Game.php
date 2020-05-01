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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $temp_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $question_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $answer;

    /**
     * @ORM\Column(type="integer")
     */
    private $biggame_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BigGame", inversedBy="game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bigGame;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
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

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): self
    {
        $this->question_id = $question_id;

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

    public function getBiggameId(): ?int
    {
        return $this->biggame_id;
    }

    public function setBiggameId(int $biggame_id): self
    {
        $this->biggame_id = $biggame_id;

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
}
