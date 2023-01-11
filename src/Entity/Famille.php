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

    #[ORM\OneToMany(mappedBy: 'famille', targetEntity: Article::class)]
    private Collection $articles;

    

    public function __construct()
    {
        $this->sousFamilles = new ArrayCollection();
        $this->articles = new ArrayCollection();
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
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setFamille($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getFamille() === $this) {
                $article->setFamille(null);
            }
        }

        return $this;
    }

    
}
