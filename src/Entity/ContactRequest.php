<?php

namespace App\Entity;

use App\Repository\ContactRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
class ContactRequest
{
    const REQUEST_CONTACT_PENDING    = 1;
    const REQUEST_CONTACT_ACCEPTED   = 2;
    const REQUEST_CONTACT_REFUSED    = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $flag_state;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'requestsContact')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $source;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $target;

    /**
     * @param $source
     * @param $target
     */
    public function __construct($source, $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSource(): ?User
    {
        return $this->source;
    }

    public function setSource(?User $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): self
    {
        $this->target = $target;

        return $this;
    }

}
