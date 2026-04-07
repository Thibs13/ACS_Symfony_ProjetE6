<?php

namespace App\Entity;

use App\Repository\SecteuractiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecteuractiviteRepository::class)]
class Secteuractivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $sa_libelle = null;

    /**
     * @var Collection<int, Entreprise>
     */
    #[ORM\OneToMany(targetEntity: Entreprise::class, mappedBy: 'secteur')]
    private Collection $EntreprisesSecteur;

    public function __construct()
    {
        $this->EntreprisesSecteur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaLibelle(): ?string
    {
        return $this->sa_libelle;
    }

    public function setSaLibelle(string $sa_libelle): static
    {
        $this->sa_libelle = $sa_libelle;

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprisesSecteur(): Collection
    {
        return $this->EntreprisesSecteur;
    }

    public function addEntreprisesSecteur(Entreprise $entreprisesSecteur): static
    {
        if (!$this->EntreprisesSecteur->contains($entreprisesSecteur)) {
            $this->EntreprisesSecteur->add($entreprisesSecteur);
            $entreprisesSecteur->setSecteur($this);
        }

        return $this;
    }

    public function removeEntreprisesSecteur(Entreprise $entreprisesSecteur): static
    {
        if ($this->EntreprisesSecteur->removeElement($entreprisesSecteur)) {
            // set the owning side to null (unless already changed)
            if ($entreprisesSecteur->getSecteur() === $this) {
                $entreprisesSecteur->setSecteur(null);
            }
        }

        return $this;
    }
}
