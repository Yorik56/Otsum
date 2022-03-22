<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Datetime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"pseudo"}, message="There is already an account with this pseudo")
 */
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\ManyToMany(targetEntity: Partie::class, mappedBy: 'id_joueurs')]
    private $parties;

    #[ORM\OneToMany(mappedBy: 'id_joueur', targetEntity: ScoreJoueur::class)]
    private $scoresJoueur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: DemandeContact::class)]
    private $demandesContact;

    #[ORM\OneToOne(targetEntity: Avatar::class, cascade: ['persist', 'remove'])]
    private $avatar;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private Datetime $lastActivityAt;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $connected;

    #[ORM\OneToMany(mappedBy: 'invitedUser', targetEntity: InvitationToPlay::class)]
    private $invitationToPlay;



    #[Pure] public function __construct()
    {
        $this->parties = new ArrayCollection();
        $this->scoresJoueur = new ArrayCollection();
        $this->demandesContact = new ArrayCollection();
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
    public function getScoreJoueurs(): ArrayCollection
    {
        return $this->scoreJoueurs;
    }

    /**
     * @param ArrayCollection $scoreJoueurs
     */
    public function setScoreJoueurs(ArrayCollection $scoreJoueurs): void
    {
        $this->scoreJoueurs = $scoreJoueurs;
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
    public function getParties(): Collection
    {
        return $this->parties;
    }
    public function addParty(Partie $party): self
    {
        if (!$this->parties->contains($party)) {
            $this->parties[] = $party;
            $party->addIdJoueur($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): self
    {
        if ($this->parties->removeElement($party)) {
            $party->removeIdJoueur($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getScoresJoueur(): Collection
    {
        return $this->scoresJoueur;
    }

    public function addScoresJoueur(ScoreJoueur $scoresJoueur): self
    {
        if (!$this->scoresJoueur->contains($scoresJoueur)) {
            $this->scoresJoueur[] = $scoresJoueur;
            $scoresJoueur->setIdJoueur($this);
        }

        return $this;
    }

    public function removeScoresJoueur(ScoreJoueur $scoresJoueur): self
    {
        if ($this->scoresJoueur->removeElement($scoresJoueur)) {
            // set the owning side to null (unless already changed)
            if ($scoresJoueur->getIdJoueur() === $this) {
                $scoresJoueur->setIdJoueur(null);
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
    public function getDemandesContact(): Collection
    {
        return $this->demandesContact;
    }

    public function addDemandesContact(DemandeContact $demandesContact): self
    {
        if (!$this->demandesContact->contains($demandesContact)) {
            $this->demandesContact[] = $demandesContact;
            $demandesContact->setSource($this);
        }

        return $this;
    }

    public function removeDemandesContact(DemandeContact $demandesContact): self
    {
        if ($this->demandesContact->removeElement($demandesContact)) {
            // set the owning side to null (unless already changed)
            if ($demandesContact->getSource() === $this) {
                $demandesContact->setSource(null);
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


}
