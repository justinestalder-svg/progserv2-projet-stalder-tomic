<?php

namespace Genres;

class GenreMusical {
    // Propriétés privées, une par colonne de la table genre_musical
    private ?int $id;
    private string $libelle;

    public function __construct(
        ?int $id,
        string $libelle
    ) {
        $this->id = $id;
        $this->setLibelle($libelle);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setLibelle(string $libelle): void {
        $libelle = trim($libelle);

        if (empty($libelle) || mb_strlen($libelle) > 50) {
            throw new \InvalidArgumentException(
                "Le libellé doit contenir entre 1 et 50 caractères."
            );
        }

        $this->libelle = $libelle;
    }
}
