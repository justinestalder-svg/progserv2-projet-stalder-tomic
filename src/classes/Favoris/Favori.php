<?php

namespace Favoris;

class Favori {

    private int $idProfil;
    private int $idExtrait;
    private \DateTimeImmutable $dateAjout;

    public function __construct(
        int $idProfil,
        int $idExtrait,
        \DateTimeImmutable $dateAjout
    ) {
        $this->setIdProfil($idProfil);
        $this->setIdExtrait($idExtrait);
        $this->setDateAjout($dateAjout);
    }

    // Getters
    public function getIdProfil(): int {
        return $this->idProfil;
    }

    public function getIdExtrait(): int {
        return $this->idExtrait;
    }

    public function getDateAjout(): \DateTimeImmutable {
        return $this->dateAjout;
    }

    // Setters
    public function setIdProfil(int $idProfil): void {
        if ($idProfil <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant du profil doit être positif."
            );
        }

        $this->idProfil = $idProfil;
    }

    public function setIdExtrait(int $idExtrait): void {
        if ($idExtrait <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant de l'extrait doit être positif."
            );
        }

        $this->idExtrait = $idExtrait;
    }

    public function setDateAjout(
        \DateTimeImmutable $dateAjout
    ): void {
        $this->dateAjout = $dateAjout;
    }
}
