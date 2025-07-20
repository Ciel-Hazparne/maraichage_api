<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    inputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    outputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    // on autorise 2 IP
    security: "is_granted('ROLE_ADMIN') and request.getClientIp() in ['127.0.0.1', '10.0.0.102']",
    // on autorise tout le réseau 10.0.0.0/16
    // security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^10\.\\d{1,3}\.\d{1,3}\.\d{1,3}$/'"
    securityMessage: "Accès restreint à l'administrateur depuis le réseau 10.0.0.0/16.",
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ]
)]

class LibelleMesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $libelle;

    #[ORM\Column(length: 255)]
    private string $unite;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getLibelle(): string
    {
        return $this->libelle;
    }
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getUnite(): string
    {
        return $this->unite;
    }
    public function setUnite(string $unite): self
    {
        $this->unite = $unite;
        return $this;
    }
}
