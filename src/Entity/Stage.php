<?php

namespace App\Entity;

use App\Repository\StageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageRepository::class)]
class Stage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $STA_DateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $STA_DateFin = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $STA_Remarque = null;

    #[ORM\Column(length: 3)]
    private ?string $STA_Remerciement = null;

    #[ORM\Column(length: 3)]
    private ?string $STA_Bilan = null;

    #[ORM\Column(length: 3)]
    private ?string $STA_Attestation = null;

    #[ORM\Column(length: 3)]
    private ?string $STA_Jury = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $STA_Commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $STA_DateRetenu = null;

    #[ORM\ManyToOne(inversedBy: 'Stages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $ETU_ID = null;

    #[ORM\ManyToOne(inversedBy: 'Stages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $ENT_ID = null;

    #[ORM\ManyToOne(inversedBy: 'stages')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'UTI_ID')]
    private ?Utilisateur $EnseignantVisite = null;

    #[ORM\ManyToOne(inversedBy: 'stages')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'UTI_ID')]
    private ?Utilisateur $EnseignantSuivi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSTADateDebut(): ?\DateTime
    {
        return $this->STA_DateDebut;
    }

    public function setSTADateDebut(\DateTime $STA_DateDebut): static
    {
        $this->STA_DateDebut = $STA_DateDebut;

        return $this;
    }

    public function getSTADateFin(): ?\DateTime
    {
        return $this->STA_DateFin;
    }

    public function setSTADateFin(\DateTime $STA_DateFin): static
    {
        $this->STA_DateFin = $STA_DateFin;

        return $this;
    }

    public function getSTARemarque(): ?string
    {
        return $this->STA_Remarque;
    }

    public function setSTARemarque(string $STA_Remarque): static
    {
        $this->STA_Remarque = $STA_Remarque;

        return $this;
    }

    public function getSTARemerciement(): ?string
    {
        return $this->STA_Remerciement;
    }

    public function setSTARemerciement(string $STA_Remerciement): static
    {
        $this->STA_Remerciement = $STA_Remerciement;

        return $this;
    }

    public function getSTABilan(): ?string
    {
        return $this->STA_Bilan;
    }

    public function setSTABilan(string $STA_Bilan): static
    {
        $this->STA_Bilan = $STA_Bilan;

        return $this;
    }

    public function getSTAAttestation(): ?string
    {
        return $this->STA_Attestation;
    }

    public function setSTAAttestation(string $STA_Attestation): static
    {
        $this->STA_Attestation = $STA_Attestation;

        return $this;
    }

    public function getSTAJury(): ?string
    {
        return $this->STA_Jury;
    }

    public function setSTAJury(string $STA_Jury): static
    {
        $this->STA_Jury = $STA_Jury;

        return $this;
    }

    public function getSTACommentaire(): ?string
    {
        return $this->STA_Commentaire;
    }

    public function setSTACommentaire(?string $STA_Commentaire): static
    {
        $this->STA_Commentaire = $STA_Commentaire;

        return $this;
    }

    public function getSTADateRetenu(): ?\DateTime
    {
        return $this->STA_DateRetenu;
    }

    public function setSTADateRetenu(?\DateTime $STA_DateRetenu): static
    {
        $this->STA_DateRetenu = $STA_DateRetenu;

        return $this;
    }

    public function getETUID(): ?Etudiant
    {
        return $this->ETU_ID;
    }

    public function setETUID(?Etudiant $ETU_ID): static
    {
        $this->ETU_ID = $ETU_ID;

        return $this;
    }

    public function getENTID(): ?Entreprise
    {
        return $this->ENT_ID;
    }

    public function setENTID(?Entreprise $ENT_ID): static
    {
        $this->ENT_ID = $ENT_ID;

        return $this;
    }

    public function getEnseignantVisite(): ?Utilisateur
    {
        return $this->EnseignantVisite;
    }

    public function setEnseignantVisite(?Utilisateur $EnseignantVisite): static
    {
        $this->EnseignantVisite = $EnseignantVisite;

        return $this;
    }

    public function getEnseignantSuivi(): ?Utilisateur
    {
        return $this->EnseignantSuivi;
    }

    public function setEnseignantSuivi(?Utilisateur $EnseignantSuivi): static
    {
        $this->EnseignantSuivi = $EnseignantSuivi;

        return $this;
    }

}
