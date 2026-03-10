<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $HIS_Date = null;

    #[ORM\Column(length: 255)]
    private ?string $HIS_AncienneValeur = null;

    #[ORM\Column(length: 255)]
    private ?string $HIS_NouvelleValeur = null;

    #[ORM\ManyToOne(inversedBy: 'Historiques')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'UTI_ID')]
    private ?Utilisateur $UTI_ID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHISDate(): ?\DateTime
    {
        return $this->HIS_Date;
    }

    public function setHISDate(\DateTime $HIS_Date): static
    {
        $this->HIS_Date = $HIS_Date;

        return $this;
    }

    public function getHISAncienneValeur(): ?string
    {
        return $this->HIS_AncienneValeur;
    }

    public function setHISAncienneValeur(string $HIS_AncienneValeur): static
    {
        $this->HIS_AncienneValeur = $HIS_AncienneValeur;

        return $this;
    }

    public function getHISNouvelleValeur(): ?string
    {
        return $this->HIS_NouvelleValeur;
    }

    public function setHISNouvelleValeur(string $HIS_NouvelleValeur): static
    {
        $this->HIS_NouvelleValeur = $HIS_NouvelleValeur;

        return $this;
    }

    public function getUTIID(): ?Utilisateur
    {
        return $this->UTI_ID;
    }

    public function setUTIID(?Utilisateur $UTI_ID): static
    {
        $this->UTI_ID = $UTI_ID;

        return $this;
    }
}
