<?php

namespace Sessions;

use Database;

class SessionEcouteManager implements SessionEcouteManagerInterface {

    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne SQL en objet SessionEcoute
    private function creerSession(array $donnees): SessionEcoute {
        return new SessionEcoute(
            (int) $donnees['id_session'],
            new \DateTimeImmutable($donnees['date_debut']),
            $donnees['statut'],
            (int) $donnees['id_profil'],
            (int) $donnees['id_serie']
        );
    }

    // Récupérer toutes les sessions
    public function getSessionsEcoute(): array {
        $sql = "SELECT * FROM session_ecoute";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerSession($ligne);
        }

        return $resultats;
    }

    // Récupérer une session par son identifiant
    public function getSessionEcouteById(int $id): ?SessionEcoute {
        $sql = "SELECT * FROM session_ecoute
                WHERE id_session = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne) {
            return $this->creerSession($ligne);
        }

        return null;
        }

    // Ajouter une session
    public function addSessionEcoute(SessionEcoute $session): int {
        $sql = "INSERT INTO session_ecoute
                    (date_debut, statut, id_profil, id_serie)
                VALUES
                    (:date_debut, :statut, :id_profil, :id_serie)";

        $pdo = $this->database->getPdo();
        $stmt = $pdo->prepare($sql);

        $this->lierValeurs($stmt, $session);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    // Modifier une session
    public function updateSessionEcoute(SessionEcoute $session): bool {
        if ($session->getId() === null) {
            throw new \InvalidArgumentException(
                "La session doit avoir un identifiant."
            );
        }

        $sql = "UPDATE session_ecoute SET
                    date_debut = :date_debut,
                    statut = :statut,
                    id_profil = :id_profil,
                    id_serie = :id_serie
                WHERE id_session = :id";

        $stmt = $this->database->getPdo()->prepare($sql);

        $this->lierValeurs($stmt, $session);
        $stmt->bindValue(
            ':id',
            $session->getId(),
            \PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    // Supprimer une session
    public function removeSessionEcoute(int $id): bool {
        $sql = "DELETE FROM session_ecoute
                WHERE id_session = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Associe les attributs PHP aux paramètres SQL
    private function lierValeurs(
        \PDOStatement $stmt,
        SessionEcoute $session
    ): void {
        $stmt->bindValue(
            ':date_debut',
            $session->getDateDebut()->format('Y-m-d H:i:s')
        );

        $stmt->bindValue(
            ':statut',
            $session->getStatut()
        );

        $stmt->bindValue(
            ':id_profil',
            $session->getIdProfil(),
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':id_serie',
            $session->getIdSerie(),
            \PDO::PARAM_INT
        );
    }
}
