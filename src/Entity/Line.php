<?php

namespace App\Entity;

use App\Repository\LineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: LineRepository::class)]
class Line
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_state;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $player;

    #[ORM\OneToMany(mappedBy: 'ligne', targetEntity: Cell::class, orphanRemoval: true)]
    private $cells;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $remaining_seconds;

    #[ORM\ManyToOne(targetEntity: Game::class, cascade: ["remove"], inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Game $game;

    #[Pure] public function __construct(Game $game)
    {
        $this->cells = new ArrayCollection();
        $this->game = $game;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlagState(): ?int
    {
        return $this->flag_state;
    }

    public function setFlagState(?int $flag_state): self
    {
        $this->flag_state = $flag_state;

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

    /**
     * @return Collection
     */
    public function getCells(): Collection
    {
        return $this->cells;
    }

    public function addCell(Cell $idCellule): self
    {
        if (!$this->cells->contains($idCellule)) {
            $this->cells[] = $idCellule;
            $idCellule->setLigne($this);
        }

        return $this;
    }

    public function removeCell(Cell $idCellule): self
    {
        if ($this->cells->removeElement($idCellule)) {
            // set the owning side to null (unless already changed)
            if ($idCellule->getLigne() === $this) {
                $idCellule->setLigne(null);
            }
        }

        return $this;
    }

    public function getRemainingSeconds(): ?int
    {
        return $this->remaining_seconds;
    }

    public function setRemainingSeconds(int $remaining_seconds): self
    {
        $this->remaining_seconds = $remaining_seconds;

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

}
