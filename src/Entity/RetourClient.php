<?php

namespace App\Entity;

use App\Repository\RetourClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourClientRepository::class)]
#[ORM\Table(name:'stock_retour_client')]
class RetourClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRetour = null;

    #[ORM\Column(length: 20,unique:true)]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'retourClient', targetEntity: LigneRetourClient::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $ligneRetourClients;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'retourClients')]
    private ?Client $client = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $telephone = null;

    public function __construct()
    {
        $this->ligneRetourClients = new ArrayCollection();
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
            $ligneRetourClient->setRetourClient($this);
        }

        return $this;
    }

    public function removeLigneRetourClient(LigneRetourClient $ligneRetourClient): self
    {
        if ($this->ligneRetourClients->removeElement($ligneRetourClient)) {
            // set the owning side to null (unless already changed)
            if ($ligneRetourClient->getRetourClient() === $this) {
                $ligneRetourClient->setRetourClient(null);
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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
}
