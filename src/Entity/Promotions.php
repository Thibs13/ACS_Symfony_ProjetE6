<?php

namespace App\Entity;

use App\Repository\PromotionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromotionsRepository::class)]
class Promotions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $pro_libelle = null;

    #[ORM\Column(length: 10)]
    private ?string $pro_session = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $pro_datedebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $pro_datefin = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'promo')]
    private Collection $StagesPromo;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\OneToMany(targetEntity: Etudiant::class, mappedBy: 'Promo')]
    private Collection $EtudiantsPromo;

    public function __construct()
    {
        $this->StagesPromo = new ArrayCollection();
        $this->EtudiantsPromo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProLibelle(): ?string
    {
        return $this->pro_libelle;
    }

    public function setProLibelle(string $pro_libelle): static
    {
        $this->pro_libelle = $pro_libelle;

        return $this;
    }

    public function getProSession(): ?string
    {
        return $this->pro_session;
    }

    public function setProSession(string $pro_session): static
    {
        $this->pro_session = $pro_session;

        return $this;
    }

    public function getProDatedebut(): ?\DateTime
    {
        return $this->pro_datedebut;
    }

    public function setProDatedebut(\DateTime $pro_datedebut): static
    {
        $this->pro_datedebut = $pro_datedebut;

        return $this;
    }

    public function getProDatefin(): ?\DateTime
    {
        return $this->pro_datefin;
    }

    public function setProDatefin(\DateTime $pro_datefin): static
    {
        $this->pro_datefin = $pro_datefin;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStagesPromo(): Collection
    {
        return $this->StagesPromo;
    }

    public function addStagesPromo(Stage $stagesPromo): static
    {
        if (!$this->StagesPromo->contains($stagesPromo)) {
            $this->StagesPromo->add($stagesPromo);
            $stagesPromo->setPromo($this);
        }

        return $this;
    }

    public function removeStagesPromo(Stage $stagesPromo): static
    {
        if ($this->StagesPromo->removeElement($stagesPromo)) {
            // set the owning side to null (unless already changed)
            if ($stagesPromo->getPromo() === $this) {
                $stagesPromo->setPromo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiantsPromo(): Collection
    {
        return $this->EtudiantsPromo;
    }

    public function addEtudiantsPromo(Etudiant $etudiantsPromo): static
    {
        if (!$this->EtudiantsPromo->contains($etudiantsPromo)) {
            $this->EtudiantsPromo->add($etudiantsPromo);
            $etudiantsPromo->setPromo($this);
        }

        return $this;
    }

    public function removeEtudiantsPromo(Etudiant $etudiantsPromo): static
    {
        if ($this->EtudiantsPromo->removeElement($etudiantsPromo)) {
            // set the owning side to null (unless already changed)
            if ($etudiantsPromo->getPromo() === $this) {
                $etudiantsPromo->setPromo(null);
            }
        }

        return $this;
    }
}
