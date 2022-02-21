<?php

namespace App\Entity;

use App\Repository\DemandeContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeContactRepository::class)]
class DemandeContact
{
    const DEMANDE_CONTACT_EN_ATTENTE = 1;
    const DEMANDE_CONTACT_ACCEPTEE   = 2;
    const DEMANDE_CONTACT_REFUSEE    = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_etat;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'demandesContact')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $source;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $cible;

    /**
     * @param $source
     * @param $cible
     */
    public function __construct($source, $cible)
    {
        $this->source = $source;
        $this->cible = $cible;
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

    public function getSource(): ?Utilisateur
    {
        return $this->source;
    }

    public function setSource(?Utilisateur $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCible(): ?Utilisateur
    {
        return $this->cible;
    }

    public function setCible(?Utilisateur $cible): self
    {
        $this->cible = $cible;

        return $this;
    }

}
