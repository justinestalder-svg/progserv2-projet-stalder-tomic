<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Genres\GenreMusical;
use Genres\GenreMusicalManager;

$genreManager = new GenreMusicalManager();

// Crée un genre musical de test
$genre = new GenreMusical(null, 'Genre Test');
$id = $genreManager->addGenreMusical($genre);

// Relit le genre depuis la base
$genreRelu = $genreManager->getGenreMusicalById($id);

// Modifie le genre
$genreRelu->setLibelle('Genre Modifié');
$genreManager->updateGenreMusical($genreRelu);

// Vérifie la modification
$genreModifie = $genreManager->getGenreMusicalById($id);

// Récupère tous les genres
$genres = $genreManager->getGenresMusicaux();

// Supprime le genre de test
$genreManager->removeGenreMusical($id);

// Vérifie la suppression
$genreSupprime = $genreManager->getGenreMusicalById($id);

if (
    $genreModifie !== null &&
    $genreModifie->getLibelle() === 'Genre Modifié' &&
    $genreSupprime === null &&
    count($genres) > 0
) {
    echo "<p>✅ Genre créé, relu, modifié et supprimé.</p>";
    echo "<p>✅ Database, autoloader, GenreMusical et GenreMusicalManager fonctionnent.</p>";
} else {
    echo "<p>❌ Une erreur est survenue pendant les tests.</p>";
}
