<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "UTI_ID")]
    private ?int $id = null;

    #[ORM\Column(name: "UTI_Nom", length: 38)]
    private ?string $nom = null;

    #[ORM\Column(name: "UTI_Prenom", length: 38)]
    private ?string $prenom = null;

    #[ORM\Column(name: "UTI_Login", length: 38)]
    private ?string $login = null;

    #[ORM\Column(name: "UTI_Password", length: 255)]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(
        name: "ROL_ID",
        referencedColumnName: "ROL_ID", 
        nullable: false
    )]
    private ?Role $role = null;

    /**
     * @var Collection<int, Historique>
     */
    #[ORM\OneToMany(targetEntity: Historique::class, mappedBy: 'UTI_ID', orphanRemoval: true)]
    private Collection $Historiques;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\OneToMany(targetEntity: Stage::class, mappedBy: 'EnseignantVisite')]
    private Collection $stages;

    public function __construct()
    {
        $this->Historiques = new ArrayCollection();
        $this->stages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Historique>
     */
    public function getHistoriques(): Collection
    {
        return $this->Historiques;
    }

    public function addHistorique(Historique $historique): static
    {
        if (!$this->Historiques->contains($historique)) {
            $this->Historiques->add($historique);
            $historique->setUTIID($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): static
    {
        if ($this->Historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getUTIID() === $this) {
                $historique->setUTIID(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->setEnseignantVisite($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        if ($this->stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getEnseignantVisite() === $this) {
                $stage->setEnseignantVisite(null);
            }
        }

        return $this;
    }
}
