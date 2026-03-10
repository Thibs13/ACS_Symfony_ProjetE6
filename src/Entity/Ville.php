<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $VIL_Nom = null;

    #[ORM\Column(length: 10)]
    private ?string $VIL_CP = null;

    /**
     * @var Collection<int, Entreprise>
     */
    #[ORM\OneToMany(targetEntity: Entreprise::class, mappedBy: 'VIL_ID')]
    private Collection $Entreprises;

    public function __construct()
    {
        $this->Entreprises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVILNom(): ?string
    {
        return $this->VIL_Nom;
    }

    public function setVILNom(string $VIL_Nom): static
    {
        $this->VIL_Nom = $VIL_Nom;

        return $this;
    }

    public function getVILCP(): ?string
    {
        return $this->VIL_CP;
    }

    public function setVILCP(string $VIL_CP): static
    {
        $this->VIL_CP = $VIL_CP;

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprises(): Collection
    {
        return $this->Entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): static
    {
        if (!$this->Entreprises->contains($entreprise)) {
            $this->Entreprises->add($entreprise);
            $entreprise->setVILID($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): static
    {
        if ($this->Entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getVILID() === $this) {
                $entreprise->setVILID(null);
            }
        }

        return $this;
    }
}
