<?php

namespace Profils;

use Database;

class ProfilManager implements ProfilManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet Profil
    private function creerProfil(array $donnees): Profil {
        return new Profil(
            $donnees['id_profil'],
            $donnees['genre'],
            new \DateTime($donnees['date_naissance']),
            $donnees['photo_profil'],
            $donnees['nom_instagram'],
            $donnees['ville'],
            $donnees['id_compte']
        );
    }

    public function getProfils(): array {
        $sql = "SELECT * FROM profil";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerProfil($ligne), $lignes);
    }

    public function getProfilById(int $id): ?Profil {
        $sql = "SELECT * FROM profil WHERE id_profil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerProfil($ligne) : null;
    }

    // Retrouve le profil d'un compte (par exemple celui de la personne connectée)
    public function getProfilByCompteId(int $idCompte): ?Profil {
        $sql = "SELECT * FROM profil WHERE id_compte = :id_compte";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_compte', $idCompte);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerProfil($ligne) : null;
    }

    public function addProfil(Profil $profil): int {
        $sql = "INSERT INTO profil (genre, date_naissance, photo_profil, nom_instagram, ville, id_compte)
                VALUES (:genre, :date_naissance, :photo_profil, :nom_instagram, :ville, :id_compte)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $profil);
        $stmt->execute();

        // Identifiant du profil qui vient d'être créé
        return (int) $this->database->getPdo()->lastInsertId();
    }

    public function updateProfil(Profil $profil): bool {
        $sql = "UPDATE profil SET genre = :genre, date_naissance = :date_naissance, photo_profil = :photo_profil,
                    nom_instagram = :nom_instagram, ville = :ville, id_compte = :id_compte
                WHERE id_profil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $profil);
        $stmt->bindValue(':id', $profil->getId());

        return $stmt->execute();
    }

    public function removeProfil(int $id): bool {
        $sql = "DELETE FROM profil WHERE id_profil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    // Lie les attributs du profil aux paramètres de la requête (utilisé par add et update)
    private function lierValeurs(\PDOStatement $stmt, Profil $profil): void {
        $stmt->bindValue(':genre', $profil->getGenre());
        $stmt->bindValue(':date_naissance', $profil->getDateNaissance()->format('Y-m-d'));
        $stmt->bindValue(':photo_profil', $profil->getPhotoProfil());
        $stmt->bindValue(':nom_instagram', $profil->getNomInstagram());
        $stmt->bindValue(':ville', $profil->getVille());
        $stmt->bindValue(':id_compte', $profil->getIdCompte(), \PDO::PARAM_INT);
    }
}
