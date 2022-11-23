<?php

namespace App\Entity;

use App\Repository\RetourFournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourFournisseurRepository::class)]
#[ORM\Table(name:'stock_retour_fournisseur')]
class RetourFournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRetour = null;

    #[ORM\Column(length: 40,unique:true)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'retourFournisseurs')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToMany(mappedBy: 'retourFournisseur', targetEntity: LigneRetourFournisseur::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $ligneRetourFournisseurs;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    public function __construct()
    {
        $this->ligneRetourFournisseurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateRetour(): ?\DateTimeInterface
    {
        return $this->dateRetour;
    }

    public function setDateRetour(\DateTimeInterface $dateRetour): self
    {
        $this->dateRetour = $dateRetour;

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
            $ligneRetourFournisseur->setRetourFournisseur($this);
        }

        return $this;
    }

    public function removeLigneRetourFournisseur(LigneRetourFournisseur $ligneRetourFournisseur): self
    {
        if ($this->ligneRetourFournisseurs->removeElement($ligneRetourFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($ligneRetourFournisseur->getRetourFournisseur() === $this) {
                $ligneRetourFournisseur->setRetourFournisseur(null);
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
