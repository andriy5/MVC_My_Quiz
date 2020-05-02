<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verified_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BigGame", mappedBy="user")
     */
    private $bigGames;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="user")
     */
    private $games;

    public function __construct()
    {
        $this->bigGames = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getVerifiedAt(): ?\DateTimeInterface
    {
        return $this->verified_at;
    }

    public function setVerifiedAt(?\DateTimeInterface $verified_at): self
    {
        $this->verified_at = $verified_at;

        return $this;
    }

    public function eraseVerifiedAt(): self
    {
        $this->verified_at = null;

        return $this;
    }

    /**
     * @return Collection|BigGame[]
     */
    public function getBigGames(): Collection
    {
        return $this->bigGames;
    }

    public function addBigGame(BigGame $bigGame): self
    {
        if (!$this->bigGames->contains($bigGame)) {
            $this->bigGames[] = $bigGame;
            $bigGame->setUser($this);
        }

        return $this;
    }

    public function removeBigGame(BigGame $bigGame): self
    {
        if ($this->bigGames->contains($bigGame)) {
            $this->bigGames->removeElement($bigGame);
            // set the owning side to null (unless already changed)
            if ($bigGame->getUser() === $this) {
                $bigGame->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setUser($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getUser() === $this) {
                $game->setUser(null);
            }
        }

        return $this;
    }
}
