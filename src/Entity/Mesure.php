<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\MesureOutput;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
/*#[ApiResource(
//    normalizationContext: ['groups' => ['mesure:read']],
    output: MesureOutput::class,
    inputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    outputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']]
)]*/
//#[ApiResource]
/*#[ApiResource(
    normalizationContext: ['groups' => ['mesure:read']],
)]*/
    #[ApiResource(
        output: MesureOutput::class,
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
            new Post(
                security: "true", // accès public
                securityMessage: "Création accessible en POST depuis l'extérieur."
            ),
            new Patch(),
            new Delete(),
        ]
    )]
class Mesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?LibelleMesure $libelleMesure = null;

    #[ORM\Column]
    private float $valeur;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleMesure(): ?LibelleMesure
    {
        return $this->libelleMesure;
    }

    public function setLibelleMesure(?LibelleMesure $libelleMesure): Mesure
    {
        $this->libelleMesure = $libelleMesure;
        return $this;
    }


//    #[Groups(['mesure:read'])]
    public function getValeur(): float
    {
        return $this->valeur;
    }
//    #[Groups(['mesure:read'])]
    public function getUnite(): ?string
    {
        return $this->libelleMesure?->getUnite();
    }

//    #[Groups(['mesure:read'])]
    public function getLibelle(): ?string
    {
        return $this->libelleMesure?->getLibelle();
    }

//    #[Groups(['mesure:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;
        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->createdAt = $date;
        return $this;
    }
}
