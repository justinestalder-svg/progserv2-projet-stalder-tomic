<?php

namespace Matchs;

use Database;

class MatchDuoManager implements MatchDuoManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet MatchDuo, avec ses scores par genre
    private function creerMatch(array $donnees): MatchDuo {
        $match = new MatchDuo(
            $donnees['id_match'],
            $donnees['score_global'],
            $donnees['nb_sons_communs'],
            new \DateTime($donnees['date_match']),
            $donnees['titre_duo'],
            $donnees['texte_analyse'],
            $donnees['id_profil_1'],
            $donnees['id_profil_2']
        );

        // On ajoute les scores par genre, qui sont dans la table score_genre
        $sql = "SELECT id_genre, score_genre FROM score_genre WHERE id_match = :id_match";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_match', $donnees['id_match']);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $ligne) {
            $match->setScoreGenre($ligne['id_genre'], $ligne['score_genre']);
        }

        return $match;
    }

    // Tous les matchs d'un profil, qu'il soit en position 1 ou 2 (pour la page « Mes matchs »)
    public function getMatchsByProfilId(int $idProfil): array {
        $sql = "SELECT * FROM matchs
                WHERE id_profil_1 = :id_profil OR id_profil_2 = :id_profil
                ORDER BY score_global DESC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil', $idProfil);
        $stmt->execute();

        $lignes = $stmt->fetchAll();
        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerMatch($ligne);
        }

        return $resultats;
    }

    public function getMatchById(int $id): ?MatchDuo {
        $sql = "SELECT * FROM matchs WHERE id_match = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne) {
            return $this->creerMatch($ligne);
        }

        return null;    
        }

    // Le match entre deux profils, peu importe l'ordre dans lequel on les donne
    public function getMatchByProfils(int $idProfilA, int $idProfilB): ?MatchDuo {
        $sql = "SELECT * FROM matchs WHERE id_profil_1 = :id_profil_1 AND id_profil_2 = :id_profil_2";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_profil_1', min($idProfilA, $idProfilB));
        $stmt->bindValue(':id_profil_2', max($idProfilA, $idProfilB));
        $stmt->execute();

        $ligne = $stmt->fetch();
        if ($ligne) {
            return $this->creerMatch($ligne);
        }

        return null;
    }

    // Enregistre le match et ses scores par genre.
    // Renvoie l'id du match, ou null si ces deux profils ont déjà un match.
    public function addMatch(MatchDuo $match): ?int {
        if ($this->getMatchByProfils($match->getIdProfil1(), $match->getIdProfil2()) !== null) {
            return null;
        }

        $pdo = $this->database->getPdo();

        // Transaction : soit le match ET ses scores sont enregistrés, soit rien du tout
        $pdo->beginTransaction();

        try {
            $sql = "INSERT INTO matchs (score_global, nb_sons_communs, date_match, titre_duo,
                        texte_analyse, id_profil_1, id_profil_2)
                    VALUES (:score_global, :nb_sons_communs, :date_match, :titre_duo,
                        :texte_analyse, :id_profil_1, :id_profil_2)";

            $stmt = $pdo->prepare($sql);
            $this->lierValeurs($stmt, $match);
            $stmt->execute();

            $idMatch = (int) $pdo->lastInsertId();
            $this->enregistrerScoresGenre($idMatch, $match->getScoresGenre());

            $pdo->commit();

            return $idMatch;
        } catch (\Exception $e) {
            // En cas d'erreur, on annule tout ce qui a été fait dans la transaction
            $pdo->rollBack();
            throw $e;
        }
    }

    public function updateMatch(MatchDuo $match): bool {
        $pdo = $this->database->getPdo();
        $pdo->beginTransaction();

        try {
            $sql = "UPDATE matchs SET score_global = :score_global, nb_sons_communs = :nb_sons_communs,
                        date_match = :date_match, titre_duo = :titre_duo, texte_analyse = :texte_analyse,
                        id_profil_1 = :id_profil_1, id_profil_2 = :id_profil_2
                    WHERE id_match = :id";

            $stmt = $pdo->prepare($sql);
            $this->lierValeurs($stmt, $match);
            $stmt->bindValue(':id', $match->getId());
            $stmt->execute();

            // On remplace les anciens scores par genre par les nouveaux
            $this->supprimerScoresGenre($match->getId());
            $this->enregistrerScoresGenre($match->getId(), $match->getScoresGenre());

            $pdo->commit();

            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function removeMatch(int $id): bool {
        $pdo = $this->database->getPdo();
        $pdo->beginTransaction();

        try {
            // D'abord les scores (ils dépendent du match), puis le match
            $this->supprimerScoresGenre($id);

            $stmt = $pdo->prepare("DELETE FROM matchs WHERE id_match = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $pdo->commit();

            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Enregistre les scores par genre d'un match dans la table score_genre
    private function enregistrerScoresGenre(int $idMatch, array $scoresGenre): void {
        $sql = "INSERT INTO score_genre (id_match, id_genre, score_genre) VALUES (:id_match, :id_genre, :score_genre)";
        $stmt = $this->database->getPdo()->prepare($sql);

        foreach ($scoresGenre as $idGenre => $score) {
            $stmt->bindValue(':id_match', $idMatch, \PDO::PARAM_INT);
            $stmt->bindValue(':id_genre', $idGenre, \PDO::PARAM_INT);
            $stmt->bindValue(':score_genre', $score, \PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    private function supprimerScoresGenre(int $idMatch): void {
        $stmt = $this->database->getPdo()->prepare("DELETE FROM score_genre WHERE id_match = :id_match");
        $stmt->bindValue(':id_match', $idMatch);
        $stmt->execute();
    }

    // Lie les attributs du match aux paramètres de la requête (utilisé par add et update)
    private function lierValeurs(\PDOStatement $stmt, MatchDuo $match): void {
        $stmt->bindValue(':score_global', $match->getScoreGlobal(), \PDO::PARAM_INT);
        $stmt->bindValue(':nb_sons_communs', $match->getNbSonsCommuns(), \PDO::PARAM_INT);
        $stmt->bindValue(':date_match', $match->getDateMatch()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':titre_duo', $match->getTitreDuo());
        $stmt->bindValue(':texte_analyse', $match->getTexteAnalyse());
        $stmt->bindValue(':id_profil_1', $match->getIdProfil1(), \PDO::PARAM_INT);
        $stmt->bindValue(':id_profil_2', $match->getIdProfil2(), \PDO::PARAM_INT);
    }
}
