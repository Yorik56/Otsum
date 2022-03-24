<?php

namespace App\Entity;

use App\Repository\InvitationToPlayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationToPlayRepository::class)]
class InvitationToPlay
{
    const REQUEST_GAME_PENDING = 1;
    const REQUEST_GAME_ACCEPTED   = 2;
    const REQUEST_GAME_REFUSED    = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'invitationToPlay')]
    #[ORM\JoinColumn(nullable: false)]
    private $game;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'invitationToPlay')]
    #[ORM\JoinColumn(nullable: false)]
    private $invitedUser;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $userWhoInvites;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_state;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getInvitedUser(): ?User
    {
        return $this->invitedUser;
    }

    public function setInvitedUser(?User $invitedUser): self
    {
        $this->invitedUser = $invitedUser;

        return $this;
    }

    public function getUserWhoInvites(): ?User
    {
        return $this->userWhoInvites;
    }

    public function setUserWhoInvites(?User $userWhoInvites): self
    {
        $this->userWhoInvites = $userWhoInvites;

        return $this;
    }

    public function getFlagState(): ?int
    {
        return $this->flag_state;
    }

    public function setFlagState(?int $flag_state): self
    {
        $this->flag_state = $flag_state;

        return $this;
    }
}
