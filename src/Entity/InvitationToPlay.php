<?php

namespace App\Entity;

use App\Repository\InvitationToPlayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationToPlayRepository::class)]
class InvitationToPlay
{
    const DEMANDE_PARTIE_EN_ATTENTE = 1;
    const DEMANDE_PARTIE_ACCEPTEE   = 2;
    const DEMANDE_PARTIE_REFUSEE    = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Partie::class, inversedBy: 'invitationToPlay')]
    #[ORM\JoinColumn(nullable: false)]
    private $partie;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'invitationToPlay')]
    #[ORM\JoinColumn(nullable: false)]
    private $invitedUser;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $userWhoInvites;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_etat;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInvitedUser(): ?Utilisateur
    {
        return $this->invitedUser;
    }

    public function setInvitedUser(?Utilisateur $invitedUser): self
    {
        $this->invitedUser = $invitedUser;

        return $this;
    }

    public function getUserWhoInvites(): ?Utilisateur
    {
        return $this->userWhoInvites;
    }

    public function setUserWhoInvites(?Utilisateur $userWhoInvites): self
    {
        $this->userWhoInvites = $userWhoInvites;

        return $this;
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
}
