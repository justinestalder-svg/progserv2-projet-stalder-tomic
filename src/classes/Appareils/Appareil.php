<?php

namespace Appareils;

class Appareil {
    // Propriétés privées (encapsulation), une par colonne de la table `appareil`
    private ?int $id;
    private ?string $nomAppareil;
    private ?string $navigateur;
    private ?string $villeConnexion;
    private \DateTime $derniereActivite;
    private \DateTime $dateConnexion;
    private \DateTime $dateExpiration;
    private string $jetonSessionHash;
    private int $idCompte;

    public function __construct(
        ?int $id,
        ?string $nomAppareil,
        ?string $navigateur,
        ?string $villeConnexion,
        \DateTime $derniereActivite,
        \DateTime $dateConnexion,
        \DateTime $dateExpiration,
        string $jetonSessionHash,
        int $idCompte
    ) {
        // La connexion doit expirer après le moment où elle a commencé
        if ($dateExpiration <= $dateConnexion) {
            throw new \InvalidArgumentException("La date d'expiration doit être après la date de connexion.");
        }

        $this->id = $id;
        $this->nomAppareil = $this->nettoyerTexte($nomAppareil);
        $this->navigateur = $this->nettoyerTexte($navigateur);
        $this->villeConnexion = $this->nettoyerTexte($villeConnexion);
        $this->derniereActivite = $derniereActivite;
        $this->dateConnexion = $dateConnexion;
        $this->dateExpiration = $dateExpiration;
        $this->jetonSessionHash = $jetonSessionHash;
        $this->idCompte = $idCompte;
    }

    // Les textes facultatifs : on enlève les espaces, une chaîne vide devient null,
    // et on coupe à 100 caractères (la taille de la colonne dans la base)
    private function nettoyerTexte(?string $texte): ?string {
        if ($texte === null || trim($texte) === '') {
            return null;
        }

        return mb_substr(trim($texte), 0, 100);
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    public function getNomAppareil(): ?string {
        return $this->nomAppareil;
    }
    public function getNavigateur(): ?string {
        return $this->navigateur;
    }
    public function getVilleConnexion(): ?string {
        return $this->villeConnexion;
    }
    public function getDerniereActivite(): \DateTime {
        return $this->derniereActivite;
    }
    public function getDateConnexion(): \DateTime {
        return $this->dateConnexion;
    }
    public function getDateExpiration(): \DateTime {
        return $this->dateExpiration;
    }
    public function getJetonSessionHash(): string {
        return $this->jetonSessionHash;
    }
    public function getIdCompte(): int {
        return $this->idCompte;
    }

    // La connexion n'est plus valable si sa date d'expiration est passée
    public function estExpire(): bool {
        return $this->dateExpiration < new \DateTime();
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    // Appelé à chaque page visitée, pour savoir quand l'appareil a été utilisé pour la dernière fois
    public function setDerniereActivite(\DateTime $derniereActivite): void {
        $this->derniereActivite = $derniereActivite;
    }

    public function setVilleConnexion(?string $villeConnexion): void {
        $this->villeConnexion = $this->nettoyerTexte($villeConnexion);
    }
}
