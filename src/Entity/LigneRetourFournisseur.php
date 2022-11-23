<?php

namespace App\Entity;

use App\Repository\LigneRetourFournisseurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneRetourFournisseurRepository::class)]
class LigneRetourFournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneRetourFournisseurs')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'ligneRetourFournisseurs')]
    private ?RetourFournisseur $retourFournisseur = null;

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

    public function getRetourFournisseur(): ?RetourFournisseur
    {
        return $this->retourFournisseur;
    }

    public function setRetourFournisseur(?RetourFournisseur $retourFournisseur): self
    {
        $this->retourFournisseur = $retourFournisseur;

        return $this;
    }
}
