<?php

namespace App\Entity;

use App\Repository\DemandeContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeContactRepository::class)]
class DemandeContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_etat;

    #[ORM\OneToOne(inversedBy: 'demandesContact', targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $source;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $cible;


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

    public function getSource(): ?Utilisateur
    {
        return $this->source;
    }

    public function setSource(Utilisateur $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCible(): ?Utilisateur
    {
        return $this->cible;
    }

    public function setCible(Utilisateur $cible): self
    {
        $this->cible = $cible;

        return $this;
    }

}
