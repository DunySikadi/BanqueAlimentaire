<?php

namespace App\Entity;

use App\Repository\SousFamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousFamilleRepository::class)]
class SousFamille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codeSF = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleSF = null;

  

    #[ORM\OneToMany(mappedBy: 'sousFamille', targetEntity: Article::class)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeSF(): ?string
    {
        return $this->codeSF;
    }

    public function setCodeSF(string $codeSF): self
    {
        $this->codeSF = $codeSF;

        return $this;
    }

    public function getLibelleSF(): ?string
    {
        return $this->libelleSF;
    }

    public function setLibelleSF(string $libelleSF): self
    {
        $this->libelleSF = $libelleSF;

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
            $article->setSousFamille($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getSousFamille() === $this) {
                $article->setSousFamille(null);
            }
        }

        return $this;
    }
}
