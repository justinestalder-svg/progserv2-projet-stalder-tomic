<?php

namespace Reactions;

use Database;

class ReactionManager implements ReactionManagerInterface {

    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne SQL en objet Reaction
    private function creerReaction(array $donnees): Reaction {
        return new Reaction(
            (int) $donnees['id_session'],
            (int) $donnees['id_extrait'],
            $donnees['choix'],
            new \DateTimeImmutable($donnees['date_reaction'])
        );
    }

    // Récupérer toutes les réactions
    public function getReactions(): array {
        $sql = "SELECT * FROM reaction";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerReaction($ligne);
        }

        return $resultats;
    }

    // Récupérer une réaction précise
    public function getReactionById(
        int $idSession,
        int $idExtrait
    ): ?Reaction {
        $sql = "SELECT * FROM reaction
                WHERE id_session = :id_session
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_session', $idSession, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne) {
            return $this->creerReaction($ligne);
        }

        return null; }

    // Ajouter une réaction
    public function addReaction(Reaction $reaction): bool {
        $sql = "INSERT INTO reaction
                    (id_session, id_extrait, choix, date_reaction)
                VALUES
                    (:id_session, :id_extrait, :choix, :date_reaction)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $reaction);

        return $stmt->execute();
    }

    // Modifier une réaction
    public function updateReaction(Reaction $reaction): bool {
        $sql = "UPDATE reaction SET
                    choix = :choix,
                    date_reaction = :date_reaction
                WHERE id_session = :id_session
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $reaction);

        return $stmt->execute();
    }

    // Supprimer une réaction
    public function removeReaction(
        int $idSession,
        int $idExtrait
    ): bool {
        $sql = "DELETE FROM reaction
                WHERE id_session = :id_session
                AND id_extrait = :id_extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_session', $idSession, \PDO::PARAM_INT);
        $stmt->bindValue(':id_extrait', $idExtrait, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Associe les attributs PHP aux paramètres SQL
    private function lierValeurs(
        \PDOStatement $stmt,
        Reaction $reaction
    ): void {
        $stmt->bindValue(
            ':id_session',
            $reaction->getIdSession(),
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':id_extrait',
            $reaction->getIdExtrait(),
            \PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':choix',
            $reaction->getChoix()
        );

        $stmt->bindValue(
            ':date_reaction',
            $reaction->getDateReaction()->format('Y-m-d H:i:s')
        );
    }
}
