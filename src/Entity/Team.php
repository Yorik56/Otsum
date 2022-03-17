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
    private $nbr_joueur;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $couleur;

    #[ORM\ManyToOne(targetEntity: Partie::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private $partie;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class)]
    private $joueurs;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->setNbrJoueur(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrJoueur(): ?int
    {
        return $this->nbr_joueur;
    }

    public function setNbrJoueur(?int $nbr_joueur): self
    {
        $this->nbr_joueur = $nbr_joueur;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;

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

    /**
     * @return Collection|Utilisateur[]
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Utilisateur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs[] = $joueur;
        }

        return $this;
    }

    public function removeJoueur(Utilisateur $joueur): self
    {
        $this->joueurs->removeElement($joueur);

        return $this;
    }
}
