<?php

namespace App\Entity;

use App\Repository\InGamePlayerStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InGamePlayerStatusRepository::class)]
class InGamePlayerStatus
{
    const FLAG_PRESENCE_TRUE       = 1;
    const FLAG_PRESENCE_FALSE      = 2;
    const FLAG_ACTUAL_PLAYER_TRUE  = 1;
    const FLAG_ACTUAL_PLAYER_FALSE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $flagPresenceInGame;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $flagActualPlayer;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $positionInTeam;

    #[ORM\OneToOne(inversedBy: 'inGamePlayerStatus', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlagPresenceInGame(): ?int
    {
        return $this->flagPresenceInGame;
    }

    public function setFlagPresenceInGame(?int $flagPresenceInGame): self
    {
        $this->flagPresenceInGame = $flagPresenceInGame;

        return $this;
    }

    public function getFlagActualPlayer(): ?int
    {
        return $this->flagActualPlayer;
    }

    public function setFlagActualPlayer(?int $flagActualPlayer): self
    {
        $this->flagActualPlayer = $flagActualPlayer;

        return $this;
    }

    public function getPositionInTeam(): ?int
    {
        return $this->positionInTeam;
    }

    public function setPositionInTeam(?int $positionInTeam): self
    {
        $this->positionInTeam = $positionInTeam;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
