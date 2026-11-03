<?php

namespace Series;

use Database;
use Extraits\Extrait;

class SerieManager implements SerieManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne SQL en objet Serie
    private function creerSerie(array $donnees): Serie {
        return new Serie(
            (int) $donnees['id_serie'],
            $donnees['nom']
        );
    }

    // Récupérer toutes les séries
    public function getSeries(): array {
        $sql = "SELECT * FROM serie";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(
            fn($ligne) => $this->creerSerie($ligne),
            $lignes
        );
    }

    // Récupérer une série par son identifiant
    public function getSerieById(int $id): ?Serie {
        $sql = "SELECT * FROM serie WHERE id_serie = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerSerie($ligne) : null;
    }

    // Ajouter une série
    public function addSerie(Serie $serie): int {
        $sql = "INSERT INTO serie (nom) VALUES (:nom)";

        $pdo = $this->database->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nom', $serie->getNom());
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    // Modifier une série
    public function updateSerie(Serie $serie): bool {
        if ($serie->getId() === null) {
            throw new \InvalidArgumentException(
                "La série doit avoir un identifiant."
            );
        }

        $sql = "UPDATE serie SET nom = :nom WHERE id_serie = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':nom', $serie->getNom());
        $stmt->bindValue(':id', $serie->getId(), \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprimer une série
    public function removeSerie(int $id): bool {
        $pdo = $this->database->getPdo();

        $pdo->beginTransaction();

        try {
            // Supprimer d'abord les associations
            $stmt = $pdo->prepare(
                "DELETE FROM serie_extrait WHERE id_serie = :id"
            );
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            // Supprimer ensuite la série
            $stmt = $pdo->prepare(
                "DELETE FROM serie WHERE id_serie = :id"
            );
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $resultat = $stmt->execute();

            $pdo->commit();
            return $resultat;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Ajouter un extrait à une série
    public function addExtraitToSerie(
        int $idSerie,
        int $idExtrait,
        int $rang
    ): bool {
        if ($rang <= 0) {
            throw new \InvalidArgumentException(
                "Le rang doit être positif."
            );
        }

        $sql = "INSERT INTO serie_extrait
                    (id_serie, id_extrait, rang)
                VALUES
                    (:id_serie, :id_extrait, :rang)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_serie', $idSerie, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);
        $stmt->bindValue(':rang', $rang, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Récupérer les extraits d'une série dans l'ordre
    public function getExtraitsBySerie(int $idSerie): array {
        $sql = "SELECT e.*
                FROM extrait e
                INNER JOIN serie_extrait se
                    ON e.id_extrait = se.id_extrait
                WHERE se.id_serie = :id_serie
                ORDER BY se.rang ASC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_serie', $idSerie, \PDO::PARAM_INT);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(
            fn($ligne) => new Extrait(
                (int) $ligne['id_extrait'],
                $ligne['titre'],
                $ligne['artiste'],
                (int) $ligne['duree_secondes'],
                $ligne['url_audio'],
                $ligne['couleur_pochette'],
                (int) $ligne['id_genre']
            ),
            $lignes
        );
    }

    // Retirer un extrait d'une série
    public function removeExtraitFromSerie(
        int $idSerie,
        int $idExtrait
    ): bool {
        $sql = "DELETE FROM serie_extrait
                WHERE id_serie = :id_serie
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_serie', $idSerie, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}
