<?php

namespace Partages;

class PartageInsta {
    // Propriétés privées (encapsulation), une par colonne de la table `partage_insta`.
    // Pas d'id : la clé primaire est le couple (partageur, destinataire).
    private int $idProfilPartageur;
    private int $idProfilDestinataire;
    private \DateTime $datePartage;

    public function __construct(int $idProfilPartageur, int $idProfilDestinataire, \DateTime $datePartage) {
        // On ne peut pas partager son Instagram avec soi-même
        if ($idProfilPartageur === $idProfilDestinataire) {
            throw new \InvalidArgumentException("On ne peut pas partager son Instagram avec soi-même.");
        }

        $this->idProfilPartageur = $idProfilPartageur;
        $this->idProfilDestinataire = $idProfilDestinataire;
        $this->datePartage = $datePartage;
    }

    // Getters (pas de setters : un partage ne se modifie pas, il se crée ou se supprime)
    public function getIdProfilPartageur(): int {
        return $this->idProfilPartageur;
    }
    public function getIdProfilDestinataire(): int {
        return $this->idProfilDestinataire;
    }
    public function getDatePartage(): \DateTime {
        return $this->datePartage;
    }
}
