<?php

namespace Idees;

use Database;
use PDO;

class IdeeDateManager implements IdeeDateManagerInterface {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->getPdo();
    }

    public function getIdeesDate(): array {
        $stmt = $this->pdo->query(
            "SELECT * FROM idee_date ORDER BY id_idee"
        );

        return $this->convertirEnObjets($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getIdeeDateById(int $idIdee): ?IdeeDate {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM idee_date WHERE id_idee = :id"
        );

        $stmt->execute([':id' => $idIdee]);
        $donnees = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donnees) {
            return $this->creerObjet($donnees);
        }

        return null;
        }

    public function getIdeesDateByGenre(int $idGenre): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM idee_date WHERE id_genre = :id"
        );

        $stmt->execute([':id' => $idGenre]);

        return $this->convertirEnObjets($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function addIdeeDate(IdeeDate $ideeDate): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO idee_date (id_genre, titre, description)
             VALUES (:genre, :titre, :description)"
        );

        return $stmt->execute([
            ':genre' => $ideeDate->getIdGenre(),
            ':titre' => $ideeDate->getTitre(),
            ':description' => $ideeDate->getDescription()
        ]);
    }

    public function updateIdeeDate(IdeeDate $ideeDate): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE idee_date
             SET id_genre = :genre,
                 titre = :titre,
                 description = :description
             WHERE id_idee = :id"
        );

        return $stmt->execute([
            ':id' => $ideeDate->getIdIdee(),
            ':genre' => $ideeDate->getIdGenre(),
            ':titre' => $ideeDate->getTitre(),
            ':description' => $ideeDate->getDescription()
        ]);
    }

    public function removeIdeeDate(int $idIdee): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM idee_date WHERE id_idee = :id"
        );

        return $stmt->execute([':id' => $idIdee]);
    }

    private function creerObjet(array $donnees): IdeeDate {
        return new IdeeDate(
            (int) $donnees['id_idee'],
            (int) $donnees['id_genre'],
            $donnees['titre'],
            $donnees['description']
        );
    }

    private function convertirEnObjets(array $lignes): array {
        $idees = [];

        foreach ($lignes as $ligne) {
            $idees[] = $this->creerObjet($ligne);
        }

        return $idees;
    }
}
