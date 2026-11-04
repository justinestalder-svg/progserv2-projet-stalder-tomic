<?php

namespace Sessions;

class SessionEcoute {
    private ?int $id;
    private \DateTimeImmutable $dateDebut;
    private string $statut;
    private int $idProfil;
    private int $idSerie;

    public function __construct(
        ?int $id,
        \DateTimeImmutable $dateDebut,
        string $statut,
        int $idProfil,
        int $idSerie
    ) {
        $this->id = $id;
        $this->setDateDebut($dateDebut);
        $this->setStatut($statut);
        $this->setIdProfil($idProfil);
        $this->setIdSerie($idSerie);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getDateDebut(): \DateTimeImmutable {
        return $this->dateDebut;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function getIdProfil(): int {
        return $this->idProfil;
    }

    public function getIdSerie(): int {
        return $this->idSerie;
    }

    // Setters
    public function setId(int $id): void {
        if ($id <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant doit être positif."
            );
        }

        $this->id = $id;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): void {
        $this->dateDebut = $dateDebut;
    }

    public function setStatut(string $statut): void {
        $statut = trim($statut);

        if ($statut === '' || mb_strlen($statut) > 20) {
            throw new \InvalidArgumentException(
                "Le statut doit contenir entre 1 et 20 caractères."
            );
        }

        $this->statut = $statut;
    }

    public function setIdProfil(int $idProfil): void {
        if ($idProfil <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant du profil doit être positif."
            );
        }

        $this->idProfil = $idProfil;
    }

    public function setIdSerie(int $idSerie): void {
        if ($idSerie <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant de la série doit être positif."
            );
        }

        $this->idSerie = $idSerie;
    }
}
