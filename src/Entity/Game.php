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

    const SOLO_GAME               = 1;
    const PRIVATE_MULTPLAYER_GAME = 2;
    const PUBLIC_MULTPLAYER_GAME  = 3;

    const VERSUS_TYPE = [
        '1' => "1 Vs 1",
        '2' => "2 Vs 2",
        '3' => "3 Vs 3",
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $startDate;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $endDate;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games')]
    private $players;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    private ?User $currentPlayer;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    private ?User $playerWinning;

    #[ORM\Column(type: 'integer')]
    private ?int $numberOfRounds;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfRoundsPlayed;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $wordToFind;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Line::class, orphanRemoval: true)]
    private $lines;

    #[ORM\Column(type: 'integer')]
    private ?int $lineSessionTime;

    #[ORM\Column(type: 'integer')]
    private ?int $lineLength;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Team::class, orphanRemoval: true)]
    private $teams;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: InvitationToPlay::class)]
    private $invitationToPlay;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flagTypeOfGame;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $host;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $versusType;

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
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

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
        return $this->currentPlayer;
    }

    public function setCurrentPlayer(?User $currentPlayer): self
    {
        $this->currentPlayer = $currentPlayer;

        return $this;
    }

    public function getPlayerWinning(): ?User
    {
        return $this->playerWinning;
    }

    public function setPlayerWinning(?User $playerWinning): self
    {
        $this->playerWinning = $playerWinning;

        return $this;
    }

    public function getNumberOfRounds(): ?int
    {
        return $this->numberOfRounds;
    }

    public function setNumberOfRounds(int $numberOfRounds): self
    {
        $this->numberOfRounds = $numberOfRounds;

        return $this;
    }

    public function getNumberOfRoundsPlayed(): ?int
    {
        return $this->numberOfRoundsPlayed;
    }

    public function setNumberOfRoundsPlayed(?int $numberOfRoundsPlayed): self
    {
        $this->numberOfRoundsPlayed = $numberOfRoundsPlayed;

        return $this;
    }

    public function getWordToFind(): ?string
    {
        return $this->wordToFind;
    }

    public function setWordToFind(string $wordToFind): self
    {
        $this->wordToFind = $wordToFind;

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
        return $this->lineSessionTime;
    }

    public function setLineSessionTime(int $lineSessionTime): self
    {
        $this->lineSessionTime = $lineSessionTime;

        return $this;
    }

    public function getLineLength(): ?int
    {
        return $this->lineLength;
    }

    public function setLineLength(int $lineLength): self
    {
        $this->lineLength = $lineLength;

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
     * @return Collection
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

    public function getFlagTypeOfGame(): ?int
    {
        return $this->flagTypeOfGame;
    }

    public function setFlagTypeOfGame(?int $flagTypeOfGame): self
    {
        $this->flagTypeOfGame = $flagTypeOfGame;

        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getVersusType(): ?int
    {
        return $this->versusType;
    }

    public function setVersusType(?int $versusType): self
    {
        $this->versusType = $versusType;

        return $this;
    }

}
