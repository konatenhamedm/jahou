<?php

namespace App\Entity;

use App\Repository\MouvementEntreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementEntreRepository::class)]
#[ORM\Table(name:'stock_mouvement_entree')]
class MouvementEntre extends Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Sortie $entree = null;

    public function __construct()
    {
        parent::__construct();
        $this->ligneEntrees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEntree(): ?Sortie
    {
        return $this->entree;
    }

    public function setEntree(?Sortie $entree): self
    {
        $this->entree = $entree;

        return $this;
    }
}
