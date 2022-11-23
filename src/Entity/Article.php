<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column(length: 12)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?int $seuil = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Modele $modele = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneMouvement::class)]
    private Collection $ligneMouvements;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneEntree::class)]
    private Collection $ligneEntrees;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneSortie::class)]
    private Collection $ligneSorties;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneRetourClient::class)]
    private Collection $ligneRetourClients;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: LigneRetourFournisseur::class)]
    private Collection $ligneRetourFournisseurs;

    #[ORM\Column]
    private ?float $prixAchat = null;

    #[ORM\Column]
    private ?float $prixVente = null;

    public function __construct()
    {
        $this->ligneMouvements = new ArrayCollection();
        $this->ligneEntrees = new ArrayCollection();
        $this->ligneSorties = new ArrayCollection();
        $this->ligneRetourClients = new ArrayCollection();
        $this->ligneRetourFournisseurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getSeuil(): ?int
    {
        return $this->seuil;
    }

    public function setSeuil(int $seuil): self
    {
        $this->seuil = $seuil;

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

    public function getModele(): ?Modele
    {
        return $this->modele;
    }

    public function setModele(?Modele $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * @return Collection<int, LigneMouvement>
     */
    public function getLigneMouvements(): Collection
    {
        return $this->ligneMouvements;
    }

    public function addLigneMouvement(LigneMouvement $ligneMouvement): self
    {
        if (!$this->ligneMouvements->contains($ligneMouvement)) {
            $this->ligneMouvements->add($ligneMouvement);
            $ligneMouvement->setArticle($this);
        }

        return $this;
    }

    public function removeLigneMouvement(LigneMouvement $ligneMouvement): self
    {
        if ($this->ligneMouvements->removeElement($ligneMouvement)) {
            // set the owning side to null (unless already changed)
            if ($ligneMouvement->getArticle() === $this) {
                $ligneMouvement->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneEntree>
     */
    public function getLigneEntrees(): Collection
    {
        return $this->ligneEntrees;
    }

    public function addLigneEntree(LigneEntree $ligneEntree): self
    {
        if (!$this->ligneEntrees->contains($ligneEntree)) {
            $this->ligneEntrees->add($ligneEntree);
            $ligneEntree->setArticle($this);
        }

        return $this;
    }

    public function removeLigneEntree(LigneEntree $ligneEntree): self
    {
        if ($this->ligneEntrees->removeElement($ligneEntree)) {
            // set the owning side to null (unless already changed)
            if ($ligneEntree->getArticle() === $this) {
                $ligneEntree->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneSortie>
     */
    public function getLigneSorties(): Collection
    {
        return $this->ligneSorties;
    }

    public function addLigneSorty(LigneSortie $ligneSorty): self
    {
        if (!$this->ligneSorties->contains($ligneSorty)) {
            $this->ligneSorties->add($ligneSorty);
            $ligneSorty->setArticle($this);
        }

        return $this;
    }

    public function removeLigneSorty(LigneSortie $ligneSorty): self
    {
        if ($this->ligneSorties->removeElement($ligneSorty)) {
            // set the owning side to null (unless already changed)
            if ($ligneSorty->getArticle() === $this) {
                $ligneSorty->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneRetourClient>
     */
    public function getLigneRetourClients(): Collection
    {
        return $this->ligneRetourClients;
    }

    public function addLigneRetourClient(LigneRetourClient $ligneRetourClient): self
    {
        if (!$this->ligneRetourClients->contains($ligneRetourClient)) {
            $this->ligneRetourClients->add($ligneRetourClient);
            $ligneRetourClient->setArticle($this);
        }

        return $this;
    }

    public function removeLigneRetourClient(LigneRetourClient $ligneRetourClient): self
    {
        if ($this->ligneRetourClients->removeElement($ligneRetourClient)) {
            // set the owning side to null (unless already changed)
            if ($ligneRetourClient->getArticle() === $this) {
                $ligneRetourClient->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneRetourFournisseur>
     */
    public function getLigneRetourFournisseurs(): Collection
    {
        return $this->ligneRetourFournisseurs;
    }

    public function addLigneRetourFournisseur(LigneRetourFournisseur $ligneRetourFournisseur): self
    {
        if (!$this->ligneRetourFournisseurs->contains($ligneRetourFournisseur)) {
            $this->ligneRetourFournisseurs->add($ligneRetourFournisseur);
            $ligneRetourFournisseur->setArticle($this);
        }

        return $this;
    }

    public function removeLigneRetourFournisseur(LigneRetourFournisseur $ligneRetourFournisseur): self
    {
        if ($this->ligneRetourFournisseurs->removeElement($ligneRetourFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($ligneRetourFournisseur->getArticle() === $this) {
                $ligneRetourFournisseur->setArticle(null);
            }
        }

        return $this;
    }

    public function getPrixAchat(): ?float
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(float $prixAchat): self
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): self
    {
        $this->prixVente = $prixVente;

        return $this;
    }
}
