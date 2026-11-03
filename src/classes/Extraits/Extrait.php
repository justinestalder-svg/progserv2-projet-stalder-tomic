<?php

namespace Extraits;

class Extrait {
    // Propriétés privées, une par colonne de la table extrait
    private ?int $id;
    private string $titre;
    private string $artiste;
    private int $dureeSecondes;
    private string $urlAudio;
    private ?string $couleurPochette;
    private int $idGenre;

    public function __construct(
        ?int $id,
        string $titre,
        string $artiste,
        int $dureeSecondes,
        string $urlAudio,
        ?string $couleurPochette,
        int $idGenre
    ) {
        $this->id = $id;
        $this->setTitre($titre);
        $this->setArtiste($artiste);
        $this->setDureeSecondes($dureeSecondes);
        $this->setUrlAudio($urlAudio);
        $this->setCouleurPochette($couleurPochette);
        $this->setIdGenre($idGenre);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    public function getTitre(): string {
        return $this->titre;
    }
    public function getArtiste(): string {
        return $this->artiste;
    }
    public function getDureeSecondes(): int {
        return $this->dureeSecondes;
    }
    public function getUrlAudio(): string {
        return $this->urlAudio;
    }
    public function getCouleurPochette(): ?string {
        return $this->couleurPochette;
    }
    public function getIdGenre(): int {
        return $this->idGenre;
    }

    // Setters, avec vérification des données
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTitre(string $titre): void {
        $titre = trim($titre);

        if ($titre === '' || mb_strlen($titre) > 150) {
            throw new \InvalidArgumentException(
                "Le titre doit contenir entre 1 et 150 caractères."
            );
        }

        $this->titre = $titre;
    }

    public function setArtiste(string $artiste): void {
        $artiste = trim($artiste);

        if ($artiste === '' || mb_strlen($artiste) > 150) {
            throw new \InvalidArgumentException(
                "Le nom de l'artiste doit contenir entre 1 et 150 caractères."
            );
        }

        $this->artiste = $artiste;
    }

    public function setDureeSecondes(int $dureeSecondes): void {
        if ($dureeSecondes <= 0) {
            throw new \InvalidArgumentException(
                "La durée doit être supérieure à 0 seconde."
            );
        }

        $this->dureeSecondes = $dureeSecondes;
    }

    public function setUrlAudio(string $urlAudio): void {
        $urlAudio = trim($urlAudio);

        if ($urlAudio === '' || mb_strlen($urlAudio) > 255) {
            throw new \InvalidArgumentException(
                "L'URL audio doit contenir entre 1 et 255 caractères."
            );
        }

        $this->urlAudio = $urlAudio;
    }

    public function setCouleurPochette(?string $couleurPochette): void {
        if (
            $couleurPochette !== null &&
            !preg_match('/^#[0-9a-fA-F]{6}$/', $couleurPochette)
        ) {
            throw new \InvalidArgumentException(
                "La couleur doit être au format hexadécimal, par exemple #FF5733."
            );
        }

        $this->couleurPochette = $couleurPochette;
    }

    public function setIdGenre(int $idGenre): void {
        if ($idGenre <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant du genre doit être positif."
            );
        }

        $this->idGenre = $idGenre;
    }
}
