<?php

namespace Partages;

use Database;

class PartageInstaManager implements PartageInstaManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet PartageInsta
    private function creerPartage(array $donnees): PartageInsta {
        return new PartageInsta(
            $donnees['id_profil_partageur'],
            $donnees['id_profil_destinataire'],
            new \DateTime($donnees['date_partage'])
        );
    }

    // Les profils qui m'ont partagé leur Instagram
    public function getPartagesRecus(int $idProfil): array {
        $sql = "SELECT * FROM partage_insta WHERE id_profil_destinataire = :id_profil ORDER BY date_partage DESC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerPartage($ligne), $lignes);
    }

    // Les profils avec qui j'ai partagé mon Instagram
    public function getPartagesEnvoyes(int $idProfil): array {
        $sql = "SELECT * FROM partage_insta WHERE id_profil_partageur = :id_profil ORDER BY date_partage DESC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerPartage($ligne), $lignes);
    }

    // Est-ce que ce profil a déjà partagé son Instagram avec cet autre profil ?
    // (sert à savoir si on peut afficher le nom Instagram)
    public function aPartage(int $idProfilPartageur, int $idProfilDestinataire): bool {
        $sql = "SELECT COUNT(*) FROM partage_insta
                WHERE id_profil_partageur = :partageur AND id_profil_destinataire = :destinataire";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':partageur', $idProfilPartageur);
        $stmt->bindValue(':destinataire', $idProfilDestinataire);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Renvoie true si le partage a été créé, false s'il existait déjà
    public function addPartage(PartageInsta $partage): bool {
        if ($this->aPartage($partage->getIdProfilPartageur(), $partage->getIdProfilDestinataire())) {
            return false;
        }

        $sql = "INSERT INTO partage_insta (id_profil_partageur, id_profil_destinataire, date_partage)
                VALUES (:partageur, :destinataire, :date_partage)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':partageur', $partage->getIdProfilPartageur(), \PDO::PARAM_INT);
        $stmt->bindValue(':destinataire', $partage->getIdProfilDestinataire(), \PDO::PARAM_INT);
        $stmt->bindValue(':date_partage', $partage->getDatePartage()->format('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    // Arrêter de partager son Instagram avec quelqu'un
    public function removePartage(int $idProfilPartageur, int $idProfilDestinataire): bool {
        $sql = "DELETE FROM partage_insta
                WHERE id_profil_partageur = :partageur AND id_profil_destinataire = :destinataire";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':partageur', $idProfilPartageur);
        $stmt->bindValue(':destinataire', $idProfilDestinataire);

        return $stmt->execute();
    }
}
