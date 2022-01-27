<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $pseudo;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\ManyToMany(targetEntity: Partie::class, mappedBy: 'id_joueurs')]
    private $parties;

    #[ORM\OneToMany(mappedBy: 'id_joueur', targetEntity: ScoreJoueur::class)]
    private $scoresJoueur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;


    public function __construct()
    {
        $this->parties = new ArrayCollection();
        $this->scoreJoueurs = new ArrayCollection();
        $this->scoresJoueur = new ArrayCollection();
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
     * @return Collection|Partie[]
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
}
