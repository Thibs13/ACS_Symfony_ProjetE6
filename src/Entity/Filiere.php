<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $FIL_Libelle = null;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\OneToMany(targetEntity: Etudiant::class, mappedBy: 'FIL_ID')]
    private Collection $Etudiants;

    public function __construct()
    {
        $this->Etudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFILLibelle(): ?string
    {
        return $this->FIL_Libelle;
    }

    public function setFILLibelle(string $FIL_Libelle): static
    {
        $this->FIL_Libelle = $FIL_Libelle;

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiants(): Collection
    {
        return $this->Etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->Etudiants->contains($etudiant)) {
            $this->Etudiants->add($etudiant);
            $etudiant->setFILID($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->Etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getFILID() === $this) {
                $etudiant->setFILID(null);
            }
        }

        return $this;
    }
}
