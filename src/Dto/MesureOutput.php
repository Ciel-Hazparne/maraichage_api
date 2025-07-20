<?php

namespace App\Dto;

final class MesureOutput
{
    public float $valeur;
    public string $unite;
    public string $libelle;
    public \DateTimeInterface $createdAt;

    public function __construct(
        float $valeur,
        string $unite,
        string $libelle,
        \DateTimeInterface $createdAt
    ) {
        $this->valeur = $valeur;
        $this->unite = $unite;
        $this->libelle = $libelle;
        $this->createdAt = $createdAt;
    }
}

