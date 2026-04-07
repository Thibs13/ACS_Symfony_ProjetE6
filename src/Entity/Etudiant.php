<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $ETU_Nom = null;

    #[ORM\Column(length: 50)]
    private ?string $ETU_Prenom = null;

    #[ORM\Column]
    private ?int $ETU_Promotion = null;

    #[ORM\ManyToOne(inversedBy: 'Etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Filiere $FIL_ID = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'ETU_ID', orphanRemoval: true)]
    private Collection $Stages;

    #[ORM\ManyToOne(inversedBy: 'EtudiantsPromo')]
    private ?Promotions $Promo = null;

    public function __construct()
    {
        $this->Stages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getETUNom(): ?string
    {
        return $this->ETU_Nom;
    }

    public function setETUNom(string $ETU_Nom): static
    {
        $this->ETU_Nom = $ETU_Nom;

        return $this;
    }

    public function getETUPrenom(): ?string
    {
        return $this->ETU_Prenom;
    }

    public function setETUPrenom(string $ETU_Prenom): static
    {
        $this->ETU_Prenom = $ETU_Prenom;

        return $this;
    }

    public function getETUPromotion(): ?int
    {
        return $this->ETU_Promotion;
    }

    public function setETUPromotion(int $ETU_Promotion): static
    {
        $this->ETU_Promotion = $ETU_Promotion;

        return $this;
    }

    public function getFILID(): ?Filiere
    {
        return $this->FIL_ID;
    }

    public function setFILID(?Filiere $FIL_ID): static
    {
        $this->FIL_ID = $FIL_ID;

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
            $stage->setETUID($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->Stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getETUID() === $this) {
                $stage->setETUID(null);
            }
        }

        return $this;
    }

    public function getPromo(): ?Promotions
    {
        return $this->Promo;
    }

    public function setPromo(?Promotions $Promo): static
    {
        $this->Promo = $Promo;

        return $this;
    }
}
