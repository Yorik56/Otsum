<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $start_date;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $end_date;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games')]
    private $players;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    private ?User $current_player;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    private ?User $player_winning;

    #[ORM\Column(type: 'integer')]
    private ?int $number_of_rounds;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $number_of_rounds_played;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $word_to_find;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Line::class, orphanRemoval: true)]
    private $lines;

    #[ORM\Column(type: 'integer')]
    private ?int $line_session_time;

    #[ORM\Column(type: 'integer')]
    private ?int $line_length;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Team::class, orphanRemoval: true)]
    private $teams;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: InvitationToPlay::class)]
    private $invitationToPlay;

    #[Pure] public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->lines = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->invitationToPlay = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeImmutable $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeImmutable $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(UserInterface $idJoueur): self
    {
        if (!$this->players->contains($idJoueur)) {
            $this->players[] = $idJoueur;
        }

        return $this;
    }

    public function removePlayer(UserInterface $idJoueur): self
    {
        $this->players->removeElement($idJoueur);

        return $this;
    }

    public function getCurrentPlayer(): ?User
    {
        return $this->current_player;
    }

    public function setCurrentPlayer(?User $current_player): self
    {
        $this->current_player = $current_player;

        return $this;
    }

    public function getPlayerWinning(): ?User
    {
        return $this->player_winning;
    }

    public function setPlayerWinning(?User $player_winning): self
    {
        $this->player_winning = $player_winning;

        return $this;
    }

    public function getNumberOfRounds(): ?int
    {
        return $this->number_of_rounds;
    }

    public function setNumberOfRounds(int $number_of_rounds): self
    {
        $this->number_of_rounds = $number_of_rounds;

        return $this;
    }

    public function getNumberOfRoundsPlayed(): ?int
    {
        return $this->number_of_rounds_played;
    }

    public function setNumberOfRoundsPlayed(?int $number_of_rounds_played): self
    {
        $this->number_of_rounds_played = $number_of_rounds_played;

        return $this;
    }

    public function getWordToFind(): ?string
    {
        return $this->word_to_find;
    }

    public function setWordToFind(string $word_to_find): self
    {
        $this->word_to_find = $word_to_find;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(Line $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setGame($this);
        }

        return $this;
    }

    public function removeLine(Line $line): self
    {
        if ($this->lines->removeElement($line)) {
            // set the owning side to null (unless already changed)
            if ($line->getGame() === $this) {
                $line->setGame(null);
            }
        }

        return $this;
    }

    public function getLineSessionTime(): ?int
    {
        return $this->line_session_time;
    }

    public function setLineSessionTime(int $line_session_time): self
    {
        $this->line_session_time = $line_session_time;

        return $this;
    }

    public function getLineLength(): ?int
    {
        return $this->line_length;
    }

    public function setLineLength(int $line_length): self
    {
        $this->line_length = $line_length;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setGame($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getGame() === $this) {
                $team->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InvitationToPlay[]
     */
    public function getInvitationToPlay(): Collection
    {
        return $this->invitationToPlay;
    }

    public function addInvitationToPlay(InvitationToPlay $invitationToPlay): self
    {
        if (!$this->invitationToPlay->contains($invitationToPlay)) {
            $this->invitationToPlay[] = $invitationToPlay;
            $invitationToPlay->setGame($this);
        }

        return $this;
    }

    public function removeInvitationToPlay(InvitationToPlay $invitationToPlay): self
    {
        if ($this->invitationToPlay->removeElement($invitationToPlay)) {
            // set the owning side to null (unless already changed)
            if ($invitationToPlay->getGame() === $this) {
                $invitationToPlay->setGame(null);
            }
        }

        return $this;
    }

}
