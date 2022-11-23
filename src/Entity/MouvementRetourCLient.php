<?php

namespace App\Entity;

use App\Repository\MouvementRetourCLientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementRetourCLientRepository::class)]
#[ORM\Table(name:'stock_mouvement_retour_client')]
class MouvementRetourCLient extends Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?RetourClient $retourClient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRetourClient(): ?RetourClient
    {
        return $this->retourClient;
    }

    public function setRetourClient(?RetourClient $retourClient): self
    {
        $this->retourClient = $retourClient;

        return $this;
    }
}
