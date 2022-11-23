<?php

namespace App\Entity;

use App\Repository\LigneMouvementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneMouvementRepository::class)]
#[ORM\Table(name:'stock_ligne_mouvement')]
class LigneMouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneMouvements')]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'ligneMouvements')]
    private ?Mouvement $mouvement = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(nullable: true)]
    private ?int $remise = null;

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

    public function getMouvement(): ?Mouvement
    {
        return $this->mouvement;
    }

    public function setMouvement(?Mouvement $mouvement): self
    {
        $this->mouvement = $mouvement;

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

    public function getRemise(): ?int
    {
        return $this->remise;
    }

    public function setRemise(?int $remise): self
    {
        $this->remise = $remise;

        return $this;
    }
}
