<?php

namespace Appareils;

use Database;

class AppareilManager implements AppareilManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet Appareil
    private function creerAppareil(array $donnees): Appareil {
        return new Appareil(
            $donnees['id_appareil'],
            $donnees['nom_appareil'],
            $donnees['navigateur'],
            $donnees['ville_connexion'],
            new \DateTime($donnees['derniere_activite']),
            new \DateTime($donnees['date_connexion']),
            new \DateTime($donnees['date_expiration']),
            $donnees['jeton_session_hash'],
            $donnees['id_compte']
        );
    }

    // Tous les appareils connectés à un compte (pour la page « Mes appareils »)
    public function getAppareilsByCompteId(int $idCompte): array {
        $sql = "SELECT * FROM appareil WHERE id_compte = :id_compte ORDER BY derniere_activite DESC";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_compte', $idCompte);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerAppareil($ligne), $lignes);
    }

    public function getAppareilById(int $id): ?Appareil {
        $sql = "SELECT * FROM appareil WHERE id_appareil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerAppareil($ligne) : null;
    }

    // Retrouve l'appareil à partir du cookie de session (à chaque page protégée)
    public function getAppareilByJetonSessionHash(string $jetonSessionHash): ?Appareil {
        $sql = "SELECT * FROM appareil WHERE jeton_session_hash = :jeton_session_hash";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':jeton_session_hash', $jetonSessionHash);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerAppareil($ligne) : null;
    }

    public function addAppareil(Appareil $appareil): int {
        $sql = "INSERT INTO appareil (nom_appareil, navigateur, ville_connexion, derniere_activite,
                    date_connexion, date_expiration, jeton_session_hash, id_compte)
                VALUES (:nom_appareil, :navigateur, :ville_connexion, :derniere_activite,
                    :date_connexion, :date_expiration, :jeton_session_hash, :id_compte)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $appareil);
        $stmt->execute();

        // Identifiant de l'appareil qui vient d'être enregistré
        return (int) $this->database->getPdo()->lastInsertId();
    }

    public function updateAppareil(Appareil $appareil): bool {
        $sql = "UPDATE appareil SET nom_appareil = :nom_appareil, navigateur = :navigateur,
                    ville_connexion = :ville_connexion, derniere_activite = :derniere_activite,
                    date_connexion = :date_connexion, date_expiration = :date_expiration,
                    jeton_session_hash = :jeton_session_hash, id_compte = :id_compte
                WHERE id_appareil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $appareil);
        $stmt->bindValue(':id', $appareil->getId());

        return $stmt->execute();
    }

    // Déconnecter un appareil = supprimer sa ligne
    public function removeAppareil(int $id): bool {
        $sql = "DELETE FROM appareil WHERE id_appareil = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    // « Déconnecter tous les autres appareils » : on garde seulement celui qu'on utilise
    public function removeAutresAppareils(int $idCompte, int $idAppareilActuel): bool {
        $sql = "DELETE FROM appareil WHERE id_compte = :id_compte AND id_appareil != :id_appareil";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_compte', $idCompte);
        $stmt->bindValue(':id_appareil', $idAppareilActuel);

        return $stmt->execute();
    }

    // Lie les attributs de l'appareil aux paramètres de la requête (utilisé par add et update)
    private function lierValeurs(\PDOStatement $stmt, Appareil $appareil): void {
        $stmt->bindValue(':nom_appareil', $appareil->getNomAppareil());
        $stmt->bindValue(':navigateur', $appareil->getNavigateur());
        $stmt->bindValue(':ville_connexion', $appareil->getVilleConnexion());
        $stmt->bindValue(':derniere_activite', $appareil->getDerniereActivite()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':date_connexion', $appareil->getDateConnexion()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':date_expiration', $appareil->getDateExpiration()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':jeton_session_hash', $appareil->getJetonSessionHash());
        $stmt->bindValue(':id_compte', $appareil->getIdCompte(), \PDO::PARAM_INT);
    }
}
