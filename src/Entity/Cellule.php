<?php

namespace App\Entity;

use App\Repository\CelluleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CelluleRepository::class)]
class Cellule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_testee;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_presente;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $flag_placee;

    #[ORM\Column(type: 'string', length: 1)]
    private $valeur;

    #[ORM\Column(type: 'integer')]
    private $position;

    #[ORM\ManyToOne(targetEntity: Ligne::class, cascade: ["remove"], inversedBy: 'id_cellules')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $ligne;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlagTestee(): ?int
    {
        return $this->flag_testee;
    }

    public function setFlagTestee(?int $flag_testee): self
    {
        $this->flag_testee = $flag_testee;

        return $this;
    }

    public function getFlagPresente(): ?int
    {
        return $this->flag_presente;
    }

    public function setFlagPresente(?int $flag_presente): self
    {
        $this->flag_presente = $flag_presente;

        return $this;
    }

    public function getFlagPlacee(): ?int
    {
        return $this->flag_placee;
    }

    public function setFlagPlacee(?int $flag_placee): self
    {
        $this->flag_placee = $flag_placee;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getLigne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setLigne(?Ligne $ligne): self
    {
        $this->ligne = $ligne;

        return $this;
    }
}
