<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;


    #[ORM\Column(length: 255)]
    private ?string $adresseEmail = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Vente::class)]
    private Collection $ventes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: RetourClient::class)]
    private Collection $retourClients;



    public function __construct()
    {

        $this->ventes = new ArrayCollection();
        $this->retourClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

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


    public function getAdresseEmail(): ?string
    {
        return $this->adresseEmail;
    }

    public function setAdresseEmail(string $adresseEmail): self
    {
        $this->adresseEmail = $adresseEmail;

        return $this;
    }

    /**
     * @return Collection<int, Vente>
     */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }

    public function addVente(Vente $vente): self
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes->add($vente);
            $vente->setClient($this);
        }

        return $this;
    }

    public function removeVente(Vente $vente): self
    {
        if ($this->ventes->removeElement($vente)) {
            // set the owning side to null (unless already changed)
            if ($vente->getClient() === $this) {
                $vente->setClient(null);
            }
        }

        return $this;
    }

    public function getFullName(){
        return $this->nom.' '.$this->prenoms;
    }

    /**
     * @return Collection<int, RetourClient>
     */
    public function getRetourClients(): Collection
    {
        return $this->retourClients;
    }

    public function addRetourClient(RetourClient $retourClient): self
    {
        if (!$this->retourClients->contains($retourClient)) {
            $this->retourClients->add($retourClient);
            $retourClient->setClient($this);
        }

        return $this;
    }

    public function removeRetourClient(RetourClient $retourClient): self
    {
        if ($this->retourClients->removeElement($retourClient)) {
            // set the owning side to null (unless already changed)
            if ($retourClient->getClient() === $this) {
                $retourClient->setClient(null);
            }
        }

        return $this;
    }

}
