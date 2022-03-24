<?php

namespace App\Entity;

use App\Repository\ScoreJoueurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreJoueurRepository::class)]
class PlayerScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Game::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $game;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'scoresPlayer')]
    #[ORM\JoinColumn(nullable: false)]
    private $player;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $number_of_lines_found;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getNumberOfLinesFound(): ?int
    {
        return $this->number_of_lines_found;
    }

    public function setNumberOfLinesFound(?int $number_of_lines_found): self
    {
        $this->number_of_lines_found = $number_of_lines_found;

        return $this;
    }

}
