<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: SortieRepository::class)]
#[ORM\Table(name:'stock_achat')]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on:'create')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSortie = null;

    #[ORM\Column(length: 10,unique:true)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'entree', targetEntity: LigneEntree::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $ligneEntrees;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    public function __construct()
    {
        $this->ligneEntrees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

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

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $ligneEntree->setEntree($this);
        }

        return $this;
    }

    public function removeLigneEntree(LigneEntree $ligneEntree): self
    {
        if ($this->ligneEntrees->removeElement($ligneEntree)) {
            // set the owning side to null (unless already changed)
            if ($ligneEntree->getEntree() === $this) {
                $ligneEntree->setEntree(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
