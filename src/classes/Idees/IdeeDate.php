<?php

namespace Idees;

use InvalidArgumentException;

class IdeeDate {
    private int $idIdee;
    private int $idGenre;
    private string $titre;
    private string $description;

    public function __construct(
        int $idIdee,
        int $idGenre,
        string $titre,
        string $description
    ) {
        $this->setIdIdee($idIdee);
        $this->setIdGenre($idGenre);
        $this->setTitre($titre);
        $this->setDescription($description);
    }

    public function getIdIdee(): int {
        return $this->idIdee;
    }

    public function setIdIdee(int $idIdee): void {
        if ($idIdee <= 0) {
            throw new InvalidArgumentException("Identifiant invalide.");
        }

        $this->idIdee = $idIdee;
    }

    public function getIdGenre(): int {
        return $this->idGenre;
    }

    public function setIdGenre(int $idGenre): void {
        if ($idGenre <= 0) {
            throw new InvalidArgumentException("Genre invalide.");
        }

        $this->idGenre = $idGenre;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function setTitre(string $titre): void {
        if (trim($titre) === '') {
            throw new InvalidArgumentException("Le titre est obligatoire.");
        }

        $this->titre = $titre;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        if (trim($description) === '') {
            throw new InvalidArgumentException("La description est obligatoire.");
        }

        $this->description = $description;
    }
}
