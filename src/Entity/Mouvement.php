<?php

namespace App\Entity;

use App\Repository\MouvementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: MouvementRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name:"discr", type:"string")]
#[UniqueEntity(fields: 'reference', message: 'Ce code est déjà associé à un mouvement')]
#[ORM\Table(name:'stock_mouvement')]
class Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'mouvements')]
    private ?Sens $sens = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMouvement = null;

    #[ORM\Column(length: 40)]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'mouvement', targetEntity: LigneMouvement::class,orphanRemoval: true, cascade:['persist'])]
    private Collection $ligneMouvements;

    public function __construct()
    {
        $this->ligneMouvements = new ArrayCollection();
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

    public function getSens(): ?Sens
    {
        return $this->sens;
    }

    public function setSens(?Sens $sens): self
    {
        $this->sens = $sens;

        return $this;
    }

    public function getDateMouvement(): ?\DateTimeInterface
    {
        return $this->dateMouvement;
    }

    public function setDateMouvement(\DateTimeInterface $dateMouvement): self
    {
        $this->dateMouvement = $dateMouvement;

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
            $ligneMouvement->setMouvement($this);
        }

        return $this;
    }

    public function removeLigneMouvement(LigneMouvement $ligneMouvement): self
    {
        if ($this->ligneMouvements->removeElement($ligneMouvement)) {
            // set the owning side to null (unless already changed)
            if ($ligneMouvement->getMouvement() === $this) {
                $ligneMouvement->setMouvement(null);
            }
        }

        return $this;
    }
}
