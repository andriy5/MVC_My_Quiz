<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BigGameRepository")
 */
class BigGame
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="bigGames")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="bigGame", fetch="EAGER")
     */
    private $game;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $results;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $temp_id;

    public function __construct()
    {
        $this->game = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user->getId();

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGame(): Collection
    {
        return $this->game;
    }

    public function addGame(Game $game): self
    {
        if (!$this->game->contains($game)) {
            $this->game[] = $game;
            $game->setBigGame($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->game->contains($game)) {
            $this->game->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getBigGame() === $this) {
                $game->setBigGame(null);
            }
        }

        return $this;
    }

    public function getResults(): ?int
    {
        return $this->results;
    }

    public function setResults(?int $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getTempId(): ?string
    {
        return $this->temp_id;
    }

    public function setTempId(?string $temp_id): self
    {
        $this->temp_id = $temp_id;

        return $this;
    }
}
