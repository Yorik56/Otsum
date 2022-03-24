<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $number_of_player;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $color;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private $game;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->setNumberOfPlayer(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOfPlayer(): ?int
    {
        return $this->number_of_player;
    }

    public function setNumberOfPlayer(?int $number_of_player): self
    {
        $this->number_of_player = $number_of_player;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(User $player): self
    {
        $this->players->removeElement($player);

        return $this;
    }
}
