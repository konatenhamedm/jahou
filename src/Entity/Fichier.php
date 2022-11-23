<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: FichierRepository::class)]
#[Table(name: 'param_fichier')]
class Fichier
{
    const DEFAULT_MIME_TYPES = [
        'text/plain'
        , 'application/octet-stream'
        , 'application/pdf'
        , 'application/vnd.ms-excel'
        , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        , 'application/msword'
        , 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $alt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 5)]
    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }


    public function getFileName()
    {
        return basename($this->getFullFileName());
    }


    protected function getUploadBaseDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__ . '/../../public/uploads/';
    }


    /**
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->getUploadBaseDir() . $this->getPath();
    }

    /**
     * @return mixed
     */
    public function getFullFileName()
    {
       
        if ($this->getUrl() != 'link') {
            $fileName = file_exists($this->getFullPath() . '/' . $this->alt) ?
            $this->getFullPath() . '/' . $this->alt :
            $this->getFullPath() . '/' . $this->id . '.' . $this->url;
        } else {
            $fileName = $this->getAlt();
        }
       
        return $fileName;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
