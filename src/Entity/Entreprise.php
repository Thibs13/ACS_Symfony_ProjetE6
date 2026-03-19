<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $ENT_Nom = null;

    #[ORM\Column(length: 15)]
    private ?string $ENT_Telephone = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ENT_Email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ENT_Adresse = null;

    #[ORM\ManyToOne(inversedBy: 'Entreprises')]
    private ?Ville $VIL_ID = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'ENT_ID', orphanRemoval: true)]
    private Collection $Stages;

    public function __construct()
    {
        $this->Stages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getENTNom(): ?string
    {
        return $this->ENT_Nom;
    }

    public function setENTNom(string $ENT_Nom): static
    {
        $this->ENT_Nom = $ENT_Nom;

        return $this;
    }

    public function getENTTelephone(): ?string
    {
        return $this->ENT_Telephone;
    }

    public function setENTTelephone(string $ENT_Telephone): static
    {
        $this->ENT_Telephone = $ENT_Telephone;

        return $this;
    }

    public function getENTEmail(): ?string
    {
        return $this->ENT_Email;
    }

    public function setENTEmail(?string $ENT_Email): static
    {
        $this->ENT_Email = $ENT_Email;

        return $this;
    }

    public function getENTAdresse(): ?string
    {
        return $this->ENT_Adresse;
    }

    public function setENTAdresse(?string $ENT_Adresse): static
    {
        $this->ENT_Adresse = $ENT_Adresse;

        return $this;
    }

    public function getVILID(): ?Ville
    {
        return $this->VIL_ID;
    }

    public function setVILID(?Ville $VIL_ID): static
    {
        $this->VIL_ID = $VIL_ID;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->Stages;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->Stages->contains($stage)) {
            $this->Stages->add($stage);
            $stage->setENTID($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->Stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getENTID() === $this) {
                $stage->setENTID(null);
            }
        }

        return $this;
    }
}
