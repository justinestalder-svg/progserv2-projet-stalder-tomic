<?php

namespace Jetons;

class JetonVerification {
    // Types de liens envoyés par mail
    const TYPES = ['confirmation_email', 'reinitialisation_mot_de_passe', 'changement_email'];

    // Propriétés privées (encapsulation), une par colonne de la table `jeton_verification`
    private ?int $id;
    private string $type;
    private string $valeurHash;
    private ?string $emailCible;
    private \DateTime $dateCreation;
    private \DateTime $dateExpiration;
    private ?\DateTime $dateUtilisation;
    private int $idCompte;

    public function __construct(
        ?int $id,
        string $type,
        string $valeurHash,
        ?string $emailCible,
        \DateTime $dateCreation,
        \DateTime $dateExpiration,
        ?\DateTime $dateUtilisation,
        int $idCompte
    ) {
        // La date d'expiration doit être après la date de création
        if ($dateExpiration <= $dateCreation) {
            throw new \InvalidArgumentException("La date d'expiration doit être après la date de création.");
        }

        $this->id = $id;
        $this->setType($type);
        $this->valeurHash = $valeurHash;
        $this->setEmailCible($emailCible);
        $this->dateCreation = $dateCreation;
        $this->dateExpiration = $dateExpiration;
        $this->dateUtilisation = $dateUtilisation;
        $this->idCompte = $idCompte;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    public function getType(): string {
        return $this->type;
    }
    public function getValeurHash(): string {
        return $this->valeurHash;
    }
    public function getEmailCible(): ?string {
        return $this->emailCible;
    }
    public function getDateCreation(): \DateTime {
        return $this->dateCreation;
    }
    public function getDateExpiration(): \DateTime {
        return $this->dateExpiration;
    }
    public function getDateUtilisation(): ?\DateTime {
        return $this->dateUtilisation;
    }
    public function getIdCompte(): int {
        return $this->idCompte;
    }

    // Le lien n'est plus valable si sa date d'expiration est passée
    public function estExpire(): bool {
        return $this->dateExpiration < new \DateTime();
    }

    // Le lien a déjà servi si une date d'utilisation est enregistrée
    public function estUtilise(): bool {
        return $this->dateUtilisation !== null;
    }

    // Setters, avec vérification des données
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setType(string $type): void {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException("Le type de jeton n'est pas valide.");
        }

        $this->type = $type;
    }

    public function setEmailCible(?string $emailCible): void {
        // L'email cible est facultatif (utilisé seulement pour un changement d'adresse)
        if ($emailCible !== null && !filter_var($emailCible, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'adresse e-mail cible n'est pas valide.");
        }

        $this->emailCible = $emailCible;
    }

    public function setDateUtilisation(?\DateTime $dateUtilisation): void {
        $this->dateUtilisation = $dateUtilisation;
    }
}
