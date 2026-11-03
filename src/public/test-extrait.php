<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Extraits\Extrait;
use Extraits\ExtraitManager;
use Genres\GenreMusical;
use Genres\GenreMusicalManager;

$extraitManager = new ExtraitManager();
$genreManager = new GenreMusicalManager();

$idExtrait = null;
$idGenre = null;

try {
    // Crée un genre musical pour le test
    $genre = new GenreMusical(null, 'Genre Test Extrait');
    $idGenre = $genreManager->addGenreMusical($genre);

    // Crée un extrait de test
    $extrait = new Extrait(
        null,
        'Musique Test',
        'Artiste Test',
        30,
        '/audio/test.mp3',
        '#FF5733',
        $idGenre
    );

    $idExtrait = $extraitManager->addExtrait($extrait);

    // Relit l'extrait depuis la base
    $extraitRelu = $extraitManager->getExtraitById($idExtrait);

    if ($extraitRelu === null || $extraitRelu->getTitre() !== 'Musique Test') {
        throw new Exception('Erreur lors de la lecture de l’extrait.');
    }

    echo "<p>✅ Extrait créé et relu.</p>";

    // Modifie l'extrait
    $extraitRelu->setTitre('Musique Modifiée');
    $extraitManager->updateExtrait($extraitRelu);

    $extraitModifie = $extraitManager->getExtraitById($idExtrait);

    if ($extraitModifie === null || $extraitModifie->getTitre() !== 'Musique Modifiée') {
        throw new Exception('Erreur lors de la modification.');
    }

    echo "<p>✅ Extrait modifié.</p>";

    // Récupère tous les extraits
    $extraits = $extraitManager->getExtraits();

    $trouve = false;
    foreach ($extraits as $element) {
        if ($element->getId() === $idExtrait) {
            $trouve = true;
            break;
        }
    }

    if (!$trouve) {
        throw new Exception('Extrait absent de la liste.');
    }

    echo "<p>✅ Liste des extraits récupérée.</p>";

    // Supprime l'extrait de test
    $extraitManager->removeExtrait($idExtrait);

    if ($extraitManager->getExtraitById($idExtrait) !== null) {
        throw new Exception('Erreur lors de la suppression.');
    }

    $idExtrait = null;

    echo "<p>✅ Extrait supprimé.</p>";
    echo "<p>✅ Tous les tests Extrait ont réussi.</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Nettoie les données de test, même en cas d'erreur
    if ($idExtrait !== null) {
        $extraitManager->removeExtrait($idExtrait);
    }

    if ($idGenre !== null) {
        $genreManager->removeGenreMusical($idGenre);
    }
}
