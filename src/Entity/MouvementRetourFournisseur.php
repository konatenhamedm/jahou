<?php

namespace App\Entity;

use App\Repository\MouvementRetourFournisseurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementRetourFournisseurRepository::class)]
#[ORM\Table(name:'stock_mouvement_retour_fournisseur')]
class MouvementRetourFournisseur extends Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?RetourFournisseur $retourFournisseur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRetourFournisseur(): ?RetourFournisseur
    {
        return $this->retourFournisseur;
    }

    public function setRetourFournisseur(?RetourFournisseur $retourFournisseur): self
    {
        $this->retourFournisseur = $retourFournisseur;

        return $this;
    }
}
