<?php

namespace App\Entity;

use App\Repository\ScoreJoueurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreJoueurRepository::class)]
class ScoreJoueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Partie::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $id_partie;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'scoresJoueur')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_joueur;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nombre_lignes_trouvees;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPartie(): ?Partie
    {
        return $this->id_partie;
    }

    public function setIdPartie(Partie $id_partie): self
    {
        $this->id_partie = $id_partie;

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

    public function getNombreLignesTrouvees(): ?int
    {
        return $this->nombre_lignes_trouvees;
    }

    public function setNombreLignesTrouvees(?int $nombre_lignes_trouvees): self
    {
        $this->nombre_lignes_trouvees = $nombre_lignes_trouvees;

        return $this;
    }

}
