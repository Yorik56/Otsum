<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $date_debut;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $date_fin;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'parties')]
    private $id_joueurs;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    private ?Utilisateur $id_joueur_actuel;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    private ?Utilisateur $id_joueur_gagnant;

    #[ORM\Column(type: 'integer')]
    private ?int $nombre_tours;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nombre_tours_joues;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $mot_a_trouver;

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Ligne::class, orphanRemoval: true)]
    private $id_lignes;

    #[ORM\Column(type: 'integer')]
    private ?int $duree_session_ligne;

    #[ORM\Column(type: 'integer')]
    private ?int $longueur_lignes;

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Team::class, orphanRemoval: true)]
    private $teams;

    #[Pure] public function __construct()
    {
        $this->id_joueurs = new ArrayCollection();
        $this->id_lignes = new ArrayCollection();
        $this->teams = new ArrayCollection();
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

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeImmutable $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeImmutable $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIdJoueurs(): Collection
    {
        return $this->id_joueurs;
    }

    public function addIdJoueur(UserInterface $idJoueur): self
    {
        if (!$this->id_joueurs->contains($idJoueur)) {
            $this->id_joueurs[] = $idJoueur;
        }

        return $this;
    }

    public function removeIdJoueur(UserInterface $idJoueur): self
    {
        $this->id_joueurs->removeElement($idJoueur);

        return $this;
    }

    public function getIdJoueurActuel(): ?Utilisateur
    {
        return $this->id_joueur_actuel;
    }

    public function setIdJoueurActuel(?Utilisateur $id_joueur_actuel): self
    {
        $this->id_joueur_actuel = $id_joueur_actuel;

        return $this;
    }

    public function getIdJoueurGagnant(): ?Utilisateur
    {
        return $this->id_joueur_gagnant;
    }

    public function setIdJoueurGagnant(?Utilisateur $id_joueur_gagnant): self
    {
        $this->id_joueur_gagnant = $id_joueur_gagnant;

        return $this;
    }

    public function getNombreTours(): ?int
    {
        return $this->nombre_tours;
    }

    public function setNombreTours(int $nombre_tours): self
    {
        $this->nombre_tours = $nombre_tours;

        return $this;
    }

    public function getNombreToursJoues(): ?int
    {
        return $this->nombre_tours_joues;
    }

    public function setNombreToursJoues(?int $nombre_tours_joues): self
    {
        $this->nombre_tours_joues = $nombre_tours_joues;

        return $this;
    }

    public function getMotATrouver(): ?string
    {
        return $this->mot_a_trouver;
    }

    public function setMotATrouver(string $mot_a_trouver): self
    {
        $this->mot_a_trouver = $mot_a_trouver;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIdLignes(): Collection
    {
        return $this->id_lignes;
    }

    public function addIdLigne(Ligne $idLigne): self
    {
        if (!$this->id_lignes->contains($idLigne)) {
            $this->id_lignes[] = $idLigne;
            $idLigne->setPartie($this);
        }

        return $this;
    }

    public function removeIdLigne(Ligne $idLigne): self
    {
        if ($this->id_lignes->removeElement($idLigne)) {
            // set the owning side to null (unless already changed)
            if ($idLigne->getPartie() === $this) {
                $idLigne->setPartie(null);
            }
        }

        return $this;
    }

    public function getDureeSessionLigne(): ?int
    {
        return $this->duree_session_ligne;
    }

    public function setDureeSessionLigne(int $duree_session_ligne): self
    {
        $this->duree_session_ligne = $duree_session_ligne;

        return $this;
    }

    public function getLongueurLignes(): ?int
    {
        return $this->longueur_lignes;
    }

    public function setLongueurLignes(int $longueur_lignes): self
    {
        $this->longueur_lignes = $longueur_lignes;

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
            $team->setPartie($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getPartie() === $this) {
                $team->setPartie(null);
            }
        }

        return $this;
    }

}
