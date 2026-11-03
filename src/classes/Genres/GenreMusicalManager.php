<?php

namespace Genres;

use Database;

class GenreMusicalManager implements GenreMusicalManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Transforme une ligne de la base en objet GenreMusical
    private function creerGenreMusical(array $donnees): GenreMusical {
        return new GenreMusical(
            (int) $donnees['id_genre'],
            $donnees['libelle']
        );
    }

    // Récupérer tous les genres musicaux
    public function getGenresMusicaux(): array {
        $sql = "SELECT * FROM genre_musical";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->execute();

        $lignes = $stmt->fetchAll();

        return array_map(fn($ligne) => $this->creerGenreMusical($ligne), $lignes);
    }

    // Récupérer un genre par son identifiant
    public function getGenreMusicalById(int $id): ?GenreMusical {
        $sql = "SELECT * FROM genre_musical WHERE id_genre = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne ? $this->creerGenreMusical($ligne) : null;
    }

    // Ajouter un genre musical
    public function addGenreMusical(GenreMusical $genre): int {
        $sql = "INSERT INTO genre_musical (libelle) VALUES (:libelle)";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':libelle', $genre->getLibelle());
        $stmt->execute();

        return (int) $this->database->getPdo()->lastInsertId();
    }

    // Modifier un genre musical
    public function updateGenreMusical(GenreMusical $genre): bool {
        $sql = "UPDATE genre_musical SET libelle = :libelle WHERE id_genre = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':libelle', $genre->getLibelle());
        $stmt->bindValue(':id', $genre->getId(), \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Supprimer un genre musical
    public function removeGenreMusical(int $id): bool {
        $sql = "DELETE FROM genre_musical WHERE id_genre = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}
