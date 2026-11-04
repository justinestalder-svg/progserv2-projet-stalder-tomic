<?php

namespace Favoris;

use Database;

class FavoriManager implements FavoriManagerInterface {

    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transformer une ligne SQL en objet Favori
    private function creerFavori(array $donnees): Favori {
        return new Favori(
            (int) $donnees['id_profil'],
            (int) $donnees['id_extrait'],
            new \DateTimeImmutable($donnees['date_ajout'])
        );
    }

    // Récupérer tous les favoris
    public function getFavoris(): array {
        $sql = "SELECT * FROM favori";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerFavori($ligne);
        }

        return $resultats;
    }

    // Récupérer un favori précis
    public function getFavoriById(
        int $idProfil,
        int $idExtrait
    ): ?Favori {
        $sql = "SELECT * FROM favori
                WHERE id_profil = :id_profil
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($ligne) {
            return $this->creerFavori($ligne);
        }

        return null;
    }

    // Récupérer les favoris d'un profil
    public function getFavorisByProfil(int $idProfil): array {
        $sql = "SELECT * FROM favori
                WHERE id_profil = :id_profil
                ORDER BY date_ajout DESC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil, \PDO::PARAM_INT);
        $stmt->execute();

        $lignes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerFavori($ligne);
        }

        return $resultats;
    }

    // Ajouter un favori
    public function addFavori(Favori $favori): bool {
        $sql = "INSERT INTO favori
                    (id_profil, id_extrait, date_ajout)
                VALUES
                    (:id_profil, :id_extrait, :date_ajout)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $favori);

        return $stmt->execute();
    }

    // Modifier un favori
    public function updateFavori(Favori $favori): bool {
        $sql = "UPDATE favori
                SET date_ajout = :date_ajout
                WHERE id_profil = :id_profil
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $favori);

        return $stmt->execute();
    }

    // Supprimer un favori
    public function removeFavori(
        int $idProfil,
        int $idExtrait
    ): bool {
        $sql = "DELETE FROM favori
                WHERE id_profil = :id_profil
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Associer les attributs PHP aux paramètres SQL
    private function lierValeurs(
        \PDOStatement $stmt,
        Favori $favori
    ): void {
        $stmt->bindValue(
            ':id_profil',
            $favori->getIdProfil(),
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':id_extrait',
            $favori->getIdExtrait(),
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':date_ajout',
            $favori->getDateAjout()->format('Y-m-d H:i:s')
        );
    }
}
