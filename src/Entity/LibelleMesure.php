<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ApiResource(
    inputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    outputFormats: ['jsonld' => ['application/ld+json'], 'json' => ['application/json']],
    operations: [
        new GetCollection(security: "true"), // accès public

        new Get(security: "true"), // accès public
        new Post(
        security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^(127\\.0\\.0\\.1|10\\.0\\.0\\.\\d+)$/'",
        securityMessage: "Modification réservée à l'administrateur depuis le réseau interne."
        ),
        new Patch(
        security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^(127\\.0\\.0\\.1|10\\.0\\.0\\.\\d+)$/'",
        securityMessage: "Modification réservée à l'administrateur depuis le réseau interne."
        ),
        new Delete(
        security: "is_granted('ROLE_ADMIN') and request.getClientIp() matches '/^(127\\.0\\.0\\.1|10\\.0\\.0\\.\\d+)$/'",
        securityMessage: "Modification réservée à l'administrateur depuis le réseau interne."
        ),
    ]
)]
//#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(SearchFilter::class, properties: ['libelleMesure.libelle' => 'exact'])]
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
