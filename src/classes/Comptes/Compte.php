<?php

namespace Comptes;

class Compte {
    // Langues proposées par SoundMatch
    const LANGUES = ['fr', 'en'];

    // Propriétés privées (encapsulation), une par colonne de la table `compte`
    private ?int $id;
    private string $pseudo;
    private string $email;
    private string $motDePasseHash;
    private string $langue;
    private bool $emailConfirme;
    private \DateTime $dateInscription;
    private \DateTime $dateAcceptationCgu;
    private int $nbEchecsConnexion;
    private ?\DateTime $bloqueJusqua;

    public function __construct(
        ?int $id,
        string $pseudo,
        string $email,
        string $motDePasseHash,
        string $langue,
        bool $emailConfirme,
        \DateTime $dateInscription,
        \DateTime $dateAcceptationCgu,
        int $nbEchecsConnexion = 0,
        ?\DateTime $bloqueJusqua = null
    ) {
        $this->id = $id;
        $this->setPseudo($pseudo);
        $this->setEmail($email);
        $this->motDePasseHash = $motDePasseHash;
        $this->setLangue($langue);
        $this->emailConfirme = $emailConfirme;
        $this->dateInscription = $dateInscription;
        $this->dateAcceptationCgu = $dateAcceptationCgu;
        $this->nbEchecsConnexion = $nbEchecsConnexion;
        $this->bloqueJusqua = $bloqueJusqua;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getPseudo(): string { return $this->pseudo; }
    public function getEmail(): string { return $this->email; }
    public function getMotDePasseHash(): string { return $this->motDePasseHash; }
    public function getLangue(): string { return $this->langue; }
    public function isEmailConfirme(): bool { return $this->emailConfirme; }
    public function getDateInscription(): \DateTime { return $this->dateInscription; }
    public function getDateAcceptationCgu(): \DateTime { return $this->dateAcceptationCgu; }
    public function getNbEchecsConnexion(): int { return $this->nbEchecsConnexion; }
    public function getBloqueJusqua(): ?\DateTime { return $this->bloqueJusqua; }

    // Setters, avec vérification des données
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setPseudo(string $pseudo): void {
        $pseudo = trim($pseudo);

        if (strlen($pseudo) < 3 || strlen($pseudo) > 20) {
            throw new \InvalidArgumentException("Le pseudo doit contenir entre 3 et 20 caractères.");
        }

        $this->pseudo = $pseudo;
    }

    public function setEmail(string $email): void {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'adresse e-mail n'est pas valide.");
        }

        $this->email = $email;
    }

    public function setMotDePasseHash(string $motDePasseHash): void {
        $this->motDePasseHash = $motDePasseHash;
    }

    public function setLangue(string $langue): void {
        if (!in_array($langue, self::LANGUES)) {
            throw new \InvalidArgumentException("La langue choisie n'est pas disponible.");
        }

        $this->langue = $langue;
    }

    public function setEmailConfirme(bool $emailConfirme): void {
        $this->emailConfirme = $emailConfirme;
    }

    public function setNbEchecsConnexion(int $nbEchecsConnexion): void {
        $this->nbEchecsConnexion = $nbEchecsConnexion;
    }

    public function setBloqueJusqua(?\DateTime $bloqueJusqua): void {
        $this->bloqueJusqua = $bloqueJusqua;
    }
}
