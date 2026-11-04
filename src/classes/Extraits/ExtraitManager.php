<?php

namespace Extraits;

use Database;

class ExtraitManager implements ExtraitManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base en objet Extrait
    private function creerExtrait(array $donnees): Extrait {
        return new Extrait(
            (int) $donnees['id_extrait'],
            $donnees['titre'],
            $donnees['artiste'],
            (int) $donnees['duree_secondes'],
            $donnees['url_audio'],
            $donnees['couleur_pochette'],
            (int) $donnees['id_genre']
        );
    }

    // Récupérer tous les extraits
    public function getExtraits(): array {
        $sql = "SELECT * FROM extrait";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        $resultats = [];

        // On transforme chaque ligne de la base en objet
        foreach ($lignes as $ligne) {
            $resultats[] = $this->creerExtrait($ligne);
        }

        return $resultats;    
        }

    // Récupérer un extrait par son identifiant
    public function getExtraitById(int $id): ?Extrait {
        $sql = "SELECT * FROM extrait WHERE id_extrait = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne) {
            return $this->creerExtrait($ligne);
        }

        return null;
    }

    // Ajouter un extrait
    public function addExtrait(Extrait $extrait): int {
        $sql = "INSERT INTO extrait
                    (titre, artiste, duree_secondes, url_audio, couleur_pochette, id_genre)
                VALUES
                    (:titre, :artiste, :duree_secondes, :url_audio, :couleur_pochette, :id_genre)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $extrait);
        $stmt->execute();

        return (int) $this->database->getPdo()->lastInsertId();
    }

    // Modifier un extrait
    public function updateExtrait(Extrait $extrait): bool {
        $sql = "UPDATE extrait SET
                    titre = :titre,
                    artiste = :artiste,
                    duree_secondes = :duree_secondes,
                    url_audio = :url_audio,
                    couleur_pochette = :couleur_pochette,
                    id_genre = :id_genre
                WHERE id_extrait = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $extrait);
        $stmt->bindValue(':id', $extrait->getId(), \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprimer un extrait
    public function removeExtrait(int $id): bool {
        $sql = "DELETE FROM extrait WHERE id_extrait = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Lie les attributs de l'extrait aux paramètres SQL
    private function lierValeurs(\PDOStatement $stmt, Extrait $extrait): void {
        $stmt->bindValue(':titre', $extrait->getTitre());
        $stmt->bindValue(':artiste', $extrait->getArtiste());
        $stmt->bindValue(':duree_secondes', $extrait->getDureeSecondes(), \PDO::PARAM_INT);
        $stmt->bindValue(':url_audio', $extrait->getUrlAudio());
        $stmt->bindValue(':couleur_pochette', $extrait->getCouleurPochette());
        $stmt->bindValue(':id_genre', $extrait->getIdGenre(), \PDO::PARAM_INT);
    }
}
