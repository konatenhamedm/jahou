<?php

namespace App\Entity;

use App\Repository\LigneRetourClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneRetourClientRepository::class)]
class LigneRetourClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneRetourClients')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'ligneRetourClients')]
    private ?RetourClient $retourClient = null;

    #[ORM\Column]
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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getRetourClient(): ?RetourClient
    {
        return $this->retourClient;
    }

    public function setRetourClient(?RetourClient $retourClient): self
    {
        $this->retourClient = $retourClient;

        return $this;
    }

    public function getRemise(): ?int
    {
        return $this->remise;
    }

    public function setRemise(int $remise): self
    {
        $this->remise = $remise;

        return $this;
    }
}
