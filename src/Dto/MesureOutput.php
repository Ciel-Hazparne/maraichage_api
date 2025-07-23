<?php

namespace App\Dto;

final class MesureOutput
{
    public int  $id;
    public float $valeur;
    public string $unite;
    public string $libelle;
    public \DateTimeInterface $createdAt;

    public function __construct(
        int $id,
        float $valeur,
        string $unite,
        string $libelle,
        \DateTimeInterface $createdAt
    ) {
        $this->id = $id;
        $this->valeur = $valeur;
        $this->unite = $unite;
        $this->libelle = $libelle;
        $this->createdAt = $createdAt;
    }
}

