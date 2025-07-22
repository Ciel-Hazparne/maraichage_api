<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\MesureOutput;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    output: MesureOutput::class,
    inputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    outputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    operations: [
        new GetCollection(security: "true"), // accès public
        new Get(security: "true"), // accès public
        new Post(
            security: "true", // accès public
            securityMessage: "Création accessible en POST depuis l'extérieur."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^(127\\.0\\.0\\.1|10\\.0\\.0\\.\\d+)$/'",
            securityMessage: "Modification réservée à l'administrateur depuis le réseau interne."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^(127\\.0\\.0\\.1|10\\.0\\.0\\.\\d+)$/'",
            securityMessage: "Suppression réservée à l'administrateur depuis le réseau interne."
        ),
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(SearchFilter::class, properties: ['libelleMesure.libelle' => 'exact'])]
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

    public function getValeur(): float
    {
        return $this->valeur;
    }

    public function getUnite(): ?string
    {
        return $this->libelleMesure?->getUnite();
    }

    public function getLibelle(): ?string
    {
        return $this->libelleMesure?->getLibelle();
    }

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
