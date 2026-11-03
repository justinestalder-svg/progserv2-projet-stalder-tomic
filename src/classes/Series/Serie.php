<?php

namespace Series;

class Serie {
    private ?int $id;
    private string $nom;

    public function __construct(?int $id, string $nom) {
        $this->id = $id;
        $this->setNom($nom);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
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

    public function setNom(string $nom): void {
        $nom = trim($nom);

        if ($nom === '' || mb_strlen($nom) > 50) {
            throw new \InvalidArgumentException(
                "Le nom doit contenir entre 1 et 50 caractères."
            );
        }

        $this->nom = $nom;
    }
}
