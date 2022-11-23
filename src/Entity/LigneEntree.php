<?php

namespace App\Entity;

use App\Repository\LigneEntreeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneEntreeRepository::class)]
#[ORM\Table(name:'stock_ligne_entree')]
class LigneEntree
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneEntrees')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'ligneEntrees')]
    private ?Sortie $entree = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getEntree(): ?Sortie
    {
        return $this->entree;
    }

    public function setEntree(?Sortie $entree): self
    {
        $this->entree = $entree;

        return $this;
    }

}
