<?php

namespace Jetons;

use Database;

class JetonVerificationManager implements JetonVerificationManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet JetonVerification
    private function creerJeton(array $donnees): JetonVerification {
        return new JetonVerification(
            $donnees['id_jeton'],
            $donnees['type'],
            $donnees['valeur_hash'],
            $donnees['email_cible'],
            new \DateTime($donnees['date_creation']),
            new \DateTime($donnees['date_expiration']),
            $donnees['date_utilisation'] !== null ? new \DateTime($donnees['date_utilisation']) : null,
            $donnees['id_compte']
        );
    }

    public function getJetons(): array {
        $sql = "SELECT * FROM jeton_verification";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerJeton($ligne), $lignes);
    }

    public function getJetonById(int $id): ?JetonVerification {
        $sql = "SELECT * FROM jeton_verification WHERE id_jeton = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerJeton($ligne) : null;
    }

    // Retrouve le jeton correspondant au lien sur lequel la personne a cliqué
    public function getJetonByValeurHash(string $valeurHash): ?JetonVerification {
        $sql = "SELECT * FROM jeton_verification WHERE valeur_hash = :valeur_hash";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':valeur_hash', $valeurHash);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerJeton($ligne) : null;
    }

    public function addJeton(JetonVerification $jeton): int {
        $sql = "INSERT INTO jeton_verification (type, valeur_hash, email_cible, date_creation,
                    date_expiration, date_utilisation, id_compte)
                VALUES (:type, :valeur_hash, :email_cible, :date_creation,
                    :date_expiration, :date_utilisation, :id_compte)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $jeton);
        $stmt->execute();

        // Identifiant du jeton qui vient d'être créé
        return (int) $this->database->getPdo()->lastInsertId();
    }

    public function updateJeton(JetonVerification $jeton): bool {
        $sql = "UPDATE jeton_verification SET type = :type, valeur_hash = :valeur_hash, email_cible = :email_cible,
                    date_creation = :date_creation, date_expiration = :date_expiration,
                    date_utilisation = :date_utilisation, id_compte = :id_compte
                WHERE id_jeton = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $jeton);
        $stmt->bindValue(':id', $jeton->getId());

        return $stmt->execute();
    }

    public function removeJeton(int $id): bool {
        $sql = "DELETE FROM jeton_verification WHERE id_jeton = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    // Lie les attributs du jeton aux paramètres de la requête (utilisé par add et update)
    private function lierValeurs(\PDOStatement $stmt, JetonVerification $jeton): void {
        $stmt->bindValue(':type', $jeton->getType());
        $stmt->bindValue(':valeur_hash', $jeton->getValeurHash());
        $stmt->bindValue(':email_cible', $jeton->getEmailCible());
        $stmt->bindValue(':date_creation', $jeton->getDateCreation()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':date_expiration', $jeton->getDateExpiration()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':date_utilisation', $jeton->getDateUtilisation()?->format('Y-m-d H:i:s'));
        $stmt->bindValue(':id_compte', $jeton->getIdCompte(), \PDO::PARAM_INT);
    }
}
