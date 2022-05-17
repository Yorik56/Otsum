<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Datetime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this pseudo")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const USER_CONNECTED_TRUE  = 1;
    const USER_CONNECTED_FALSE = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255,unique: true, nullable: false )]
    private mixed $pseudo;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private $games;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: PlayerScore::class)]
    private $scoresPlayer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: ContactRequest::class)]
    private $requestsContact;

    #[ORM\OneToOne(targetEntity: Avatar::class, cascade: ['persist', 'remove'])]
    private $avatar;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private Datetime $lastActivityAt;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $connected;

    #[ORM\OneToMany(mappedBy: 'invitedUser', targetEntity: InvitationToPlay::class)]
    private $invitationToPlay;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: InGamePlayerStatus::class, cascade: ['persist', 'remove'])]
    private $inGamePlayerStatus;



    #[Pure] public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->scoresPlayer = new ArrayCollection();
        $this->requestsContact = new ArrayCollection();
        $this->invitationToPlay = new ArrayCollection();
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
    public function getUserIdentifier(): string
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getScoresPlayer(): ArrayCollection
    {
        return $this->scoresPlayer;
    }

    /**
     * @param ArrayCollection $scoresPlayer
     */
    public function setScoresPlayer(ArrayCollection $scoresPlayer): void
    {
        $this->scoresPlayer = $scoresPlayer;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return Collection
     */
    public function getGames(): Collection
    {
        return $this->games;
    }
    public function addGame(Game $games): self
    {
        if (!$this->games->contains($games)) {
            $this->games[] = $games;
            $games->addPlayer($this);
        }

        return $this;
    }

    public function removeGame(Game $games): self
    {
        if ($this->games->removeElement($games)) {
            $games->removePlayer($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getScoresJoueur(): Collection
    {
        return $this->scoresPlayer;
    }

    public function addScoresJoueur(PlayerScore $scoresPlayer): self
    {
        if (!$this->scoresPlayer->contains($scoresPlayer)) {
            $this->scoresPlayer[] = $scoresPlayer;
            $scoresPlayer->setPlayer($this);
        }

        return $this;
    }

    public function removeScoresJoueur(PlayerScore $scoresPlayer): self
    {
        if ($this->scoresPlayer->removeElement($scoresPlayer)) {
            // set the owning side to null (unless already changed)
            if ($scoresPlayer->getPlayer() === $this) {
                $scoresPlayer->setPlayer(null);
            }
        }

        return $this;
    }



    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRequestsContact(): Collection
    {
        return $this->requestsContact;
    }

    public function addRequestsContact(ContactRequest $requestsContact): self
    {
        if (!$this->requestsContact->contains($requestsContact)) {
            $this->requestsContact[] = $requestsContact;
            $requestsContact->setSource($this);
        }

        return $this;
    }

    public function removeRequestsContact(ContactRequest $requestsContact): self
    {
        if ($this->requestsContact->removeElement($requestsContact)) {
            // set the owning side to null (unless already changed)
            if ($requestsContact->getSource() === $this) {
                $requestsContact->setSource(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Datetime
     */
    public function getLastActivityAt(): Datetime
    {
        return $this->lastActivityAt;
    }

    /**
     * @param Datetime $lastActivityAt
     */
    public function setLastActivityAt(Datetime $lastActivityAt): void
    {
        $this->lastActivityAt = $lastActivityAt;
    }


    /**
     * @return Bool Whether the user is active or not
     */
    public function isActiveNow(): bool
    {
        // Delay during wich the user will be considered as still active
        $delay = new \DateTime('2 minutes ago');

        return ( $this->getLastActivityAt() > $delay );
    }

    public function getConnected(): ?bool
    {
        return $this->connected;
    }

    public function setConnected(?bool $connected): self
    {
        $this->connected = $connected;

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
            $invitationToPlay->setInvitedUser($this);
        }

        return $this;
    }

    public function removeInvitationToPlay(InvitationToPlay $invitationToPlay): self
    {
        if ($this->invitationToPlay->removeElement($invitationToPlay)) {
            // set the owning side to null (unless already changed)
            if ($invitationToPlay->getInvitedUser() === $this) {
                $invitationToPlay->setInvitedUser(null);
            }
        }

        return $this;
    }

    public function getInGamePlayerStatus(): ?InGamePlayerStatus
    {
        return $this->inGamePlayerStatus;
    }

    public function setInGamePlayerStatus(InGamePlayerStatus $inGamePlayerStatus): self
    {
        // set the owning side of the relation if necessary
        if ($inGamePlayerStatus->getUser() !== $this) {
            $inGamePlayerStatus->setUser($this);
        }

        $this->inGamePlayerStatus = $inGamePlayerStatus;

        return $this;
    }
}
