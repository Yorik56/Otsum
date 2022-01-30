<?php

namespace App\Entity;

use App\Repository\LigneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: LigneRepository::class)]
class Ligne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_etat;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $id_joueur;

    #[ORM\OneToMany(mappedBy: 'ligne', targetEntity: Cellule::class)]
    private $id_cellules;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $secondes_restantes;

    #[ORM\ManyToOne(targetEntity: Partie::class, inversedBy: 'id_lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partie $partie;

    #[Pure] public function __construct(Utilisateur $id_joueur,Partie $partie)
    {
        $this->id_cellules = new ArrayCollection();
        $this->id_joueur = $id_joueur;
        $this->partie = $partie;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlagEtat(): ?int
    {
        return $this->flag_etat;
    }

    public function setFlagEtat(?int $flag_etat): self
    {
        $this->flag_etat = $flag_etat;

        return $this;
    }

    public function getIdJoueur(): ?Utilisateur
    {
        return $this->id_joueur;
    }

    public function setIdJoueur(?Utilisateur $id_joueur): self
    {
        $this->id_joueur = $id_joueur;

        return $this;
    }

    /**
     * @return Collection|Cellule[]
     */
    public function getIdCellules(): Collection
    {
        return $this->id_cellules;
    }

    public function addIdCellule(Cellule $idCellule): self
    {
        if (!$this->id_cellules->contains($idCellule)) {
            $this->id_cellules[] = $idCellule;
            $idCellule->setLigne($this);
        }

        return $this;
    }

    public function removeIdCellule(Cellule $idCellule): self
    {
        if ($this->id_cellules->removeElement($idCellule)) {
            // set the owning side to null (unless already changed)
            if ($idCellule->getLigne() === $this) {
                $idCellule->setLigne(null);
            }
        }

        return $this;
    }

    public function getSecondesRestantes(): ?int
    {
        return $this->secondes_restantes;
    }

    public function setSecondesRestantes(int $secondes_restantes): self
    {
        $this->secondes_restantes = $secondes_restantes;

        return $this;
    }

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        return $this;
    }

}
