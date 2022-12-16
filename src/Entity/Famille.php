<?php

namespace App\Entity;

use App\Repository\FamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FamilleRepository::class)]
class Famille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codeFamille = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleFamille = null;

    #[ORM\OneToMany(mappedBy: 'famille', targetEntity: SousFamille::class , orphanRemoval:true)]
    private Collection $sousFamilles;

    public function __construct()
    {
        $this->sousFamilles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeFamille(): ?string
    {
        return $this->codeFamille;
    }

    public function setCodeFamille(string $codeFamille): self
    {
        $this->codeFamille = $codeFamille;

        return $this;
    }

    public function getLibelleFamille(): ?string
    {
        return $this->libelleFamille;
    }

    public function setLibelleFamille(string $libelleFamille): self
    {
        $this->libelleFamille = $libelleFamille;

        return $this;
    }

    /**
     * @return Collection<int, SousFamille>
     */
    public function getSousFamilles(): Collection
    {
        return $this->sousFamilles;
    }

    public function addSousFamille(SousFamille $sousFamille): self
    {
        if (!$this->sousFamilles->contains($sousFamille)) {
            $this->sousFamilles->add($sousFamille);
            $sousFamille->setFamille($this);
        }

        return $this;
    }

    public function removeSousFamille(SousFamille $sousFamille): self
    {
        if ($this->sousFamilles->removeElement($sousFamille)) {
            // set the owning side to null (unless already changed)
            if ($sousFamille->getFamille() === $this) {
                $sousFamille->setFamille(null);
            }
        }

        return $this;
    }
}
