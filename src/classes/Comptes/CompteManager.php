<?php

namespace Comptes;

use Database;

class CompteManager implements CompteManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base (tableau) en objet Compte
    private function creerCompte(array $donnees): Compte {
        return new Compte(
            $donnees['id_compte'],
            $donnees['pseudo'],
            $donnees['email'],
            $donnees['mot_de_passe_hash'],
            $donnees['langue'],
            (bool) $donnees['email_confirme'],
            new \DateTime($donnees['date_inscription']),
            new \DateTime($donnees['date_acceptation_cgu']),
            $donnees['nb_echecs_connexion'],
            $donnees['bloque_jusqua'] !== null ? new \DateTime($donnees['bloque_jusqua']) : null
        );
    }

    public function getComptes(): array {
        $sql = "SELECT * FROM compte";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerCompte($ligne), $lignes);
    }

    public function getCompteById(int $id): ?Compte {
        $sql = "SELECT * FROM compte WHERE id_compte = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerCompte($ligne) : null;
    }

    public function getCompteByEmail(string $email): ?Compte {
        $sql = "SELECT * FROM compte WHERE email = :email";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerCompte($ligne) : null;
    }

    public function addCompte(Compte $compte): int {
        $sql = "INSERT INTO compte (pseudo, email, mot_de_passe_hash, langue, email_confirme,
                    date_inscription, date_acceptation_cgu, nb_echecs_connexion, bloque_jusqua)
                VALUES (:pseudo, :email, :mot_de_passe_hash, :langue, :email_confirme,
                    :date_inscription, :date_acceptation_cgu, :nb_echecs_connexion, :bloque_jusqua)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $compte);
        $stmt->execute();

        // Identifiant du compte qui vient d'être créé
        return (int) $this->database->getPdo()->lastInsertId();
    }

    public function updateCompte(Compte $compte): bool {
        $sql = "UPDATE compte SET pseudo = :pseudo, email = :email, mot_de_passe_hash = :mot_de_passe_hash,
                    langue = :langue, email_confirme = :email_confirme, date_inscription = :date_inscription,
                    date_acceptation_cgu = :date_acceptation_cgu, nb_echecs_connexion = :nb_echecs_connexion,
                    bloque_jusqua = :bloque_jusqua
                WHERE id_compte = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierValeurs($stmt, $compte);
        $stmt->bindValue(':id', $compte->getId());

        return $stmt->execute();
    }

    public function removeCompte(int $id): bool {
        $sql = "DELETE FROM compte WHERE id_compte = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    // Lie les attributs du compte aux paramètres de la requête (utilisé par add et update)
    private function lierValeurs(\PDOStatement $stmt, Compte $compte): void {
        $stmt->bindValue(':pseudo', $compte->getPseudo());
        $stmt->bindValue(':email', $compte->getEmail());
        $stmt->bindValue(':mot_de_passe_hash', $compte->getMotDePasseHash());
        $stmt->bindValue(':langue', $compte->getLangue());
        $stmt->bindValue(':email_confirme', $compte->isEmailConfirme(), \PDO::PARAM_BOOL);
        $stmt->bindValue(':date_inscription', $compte->getDateInscription()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':date_acceptation_cgu', $compte->getDateAcceptationCgu()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':nb_echecs_connexion', $compte->getNbEchecsConnexion(), \PDO::PARAM_INT);
        $stmt->bindValue(':bloque_jusqua', $compte->getBloqueJusqua()?->format('Y-m-d H:i:s'));
    }
}
