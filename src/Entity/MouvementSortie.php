<?php

namespace App\Entity;

use App\Repository\MouvementSortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementSortieRepository::class)]
#[ORM\Table(name:'stock_mouvement_sortie')]
class MouvementSortie extends Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Vente $vente = null;

    public function __construct()
    {
        parent::__construct();
        $this->ligneSorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): self
    {
        $this->vente = $vente;

        return $this;
    }
}
