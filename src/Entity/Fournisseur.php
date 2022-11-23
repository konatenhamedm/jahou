<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denominationSocial = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseMail = null;

    #[ORM\OneToMany(mappedBy: 'Fournisseur', targetEntity: Sortie::class)]
    private Collection $sorties;

    #[ORM\OneToMany(mappedBy: 'fournisseur', targetEntity: RetourFournisseur::class)]
    private Collection $retourFournisseurs;

    public function __construct()
    {

        $this->sorties = new ArrayCollection();
        $this->retourFournisseurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenominationSocial(): ?string
    {
        return $this->denominationSocial;
    }

    public function setDenominationSocial(string $denominationSocial): self
    {
        $this->denominationSocial = $denominationSocial;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresseMail(): ?string
    {
        return $this->adresseMail;
    }

    public function setAdresseMail(string $adresseMail): self
    {
        $this->adresseMail = $adresseMail;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->setFournisseur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getFournisseur() === $this) {
                $sorty->setFournisseur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RetourFournisseur>
     */
    public function getRetourFournisseurs(): Collection
    {
        return $this->retourFournisseurs;
    }

    public function addRetourFournisseur(RetourFournisseur $retourFournisseur): self
    {
        if (!$this->retourFournisseurs->contains($retourFournisseur)) {
            $this->retourFournisseurs->add($retourFournisseur);
            $retourFournisseur->setFournisseur($this);
        }

        return $this;
    }

    public function removeRetourFournisseur(RetourFournisseur $retourFournisseur): self
    {
        if ($this->retourFournisseurs->removeElement($retourFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($retourFournisseur->getFournisseur() === $this) {
                $retourFournisseur->setFournisseur(null);
            }
        }

        return $this;
    }
}
