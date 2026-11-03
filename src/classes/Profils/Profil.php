<?php

namespace Profils;

class Profil {
    // Valeurs possibles pour le genre (à adapter selon le cahier des charges)
    const GENRES = ['femme', 'homme', 'autre'];

    // Âge minimum pour utiliser SoundMatch
    const AGE_MINIMUM = 18;

    // Propriétés privées (encapsulation), une par colonne de la table `profil`
    private ?int $id;
    private string $genre;
    private \DateTime $dateNaissance;
    private string $photoProfil;
    private string $nomInstagram;
    private ?string $ville;
    private int $idCompte;

    public function __construct(
        ?int $id,
        string $genre,
        \DateTime $dateNaissance,
        string $photoProfil,
        string $nomInstagram,
        ?string $ville,
        int $idCompte
    ) {
        $this->id = $id;
        $this->setGenre($genre);
        $this->setDateNaissance($dateNaissance);
        $this->setPhotoProfil($photoProfil);
        $this->setNomInstagram($nomInstagram);
        $this->setVille($ville);
        $this->idCompte = $idCompte;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getGenre(): string { return $this->genre; }
    public function getDateNaissance(): \DateTime { return $this->dateNaissance; }
    public function getPhotoProfil(): string { return $this->photoProfil; }
    public function getNomInstagram(): string { return $this->nomInstagram; }
    public function getVille(): ?string { return $this->ville; }
    public function getIdCompte(): int { return $this->idCompte; }

    // Calcule l'âge à partir de la date de naissance (pratique pour l'affichage)
    public function getAge(): int {
        return $this->dateNaissance->diff(new \DateTime())->y;
    }

    // Setters, avec vérification des données
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setGenre(string $genre): void {
        if (!in_array($genre, self::GENRES)) {
            throw new \InvalidArgumentException("Le genre choisi n'est pas valide.");
        }

        $this->genre = $genre;
    }

    public function setDateNaissance(\DateTime $dateNaissance): void {
        $age = $dateNaissance->diff(new \DateTime())->y;

        if ($dateNaissance > new \DateTime()) {
            throw new \InvalidArgumentException("La date de naissance ne peut pas être dans le futur.");
        } else if ($age < self::AGE_MINIMUM) {
            throw new \InvalidArgumentException("Il faut avoir au moins " . self::AGE_MINIMUM . " ans pour utiliser SoundMatch.");
        }

        $this->dateNaissance = $dateNaissance;
    }

    public function setPhotoProfil(string $photoProfil): void {
        $photoProfil = trim($photoProfil);

        if (empty($photoProfil)) {
            throw new \InvalidArgumentException("La photo de profil est obligatoire.");
        } else if (strlen($photoProfil) > 255) {
            throw new \InvalidArgumentException("Le chemin de la photo est trop long.");
        }

        $this->photoProfil = $photoProfil;
    }

    public function setNomInstagram(string $nomInstagram): void {
        // On accepte "@pseudo" comme "pseudo", et on enregistre sans le @
        $nomInstagram = ltrim(trim($nomInstagram), '@');

        // Règles d'Instagram : 1 à 30 caractères, lettres, chiffres, points et underscores
        if (!preg_match('/^[A-Za-z0-9._]{1,30}$/', $nomInstagram)) {
            throw new \InvalidArgumentException("Le nom Instagram n'est pas valide.");
        }

        $this->nomInstagram = $nomInstagram;
    }

    public function setVille(?string $ville): void {
        // La ville est facultative : une chaîne vide devient null
        $ville = $ville !== null ? trim($ville) : null;

        if ($ville === '') {
            $ville = null;
        } else if ($ville !== null && strlen($ville) > 100) {
            throw new \InvalidArgumentException("Le nom de la ville est trop long.");
        }

        $this->ville = $ville;
    }
}
