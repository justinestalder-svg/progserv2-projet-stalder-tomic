<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Series\Serie;
use Series\SerieManager;
use Genres\GenreMusical;
use Genres\GenreMusicalManager;
use Extraits\Extrait;
use Extraits\ExtraitManager;

$serieManager = new SerieManager();
$genreManager = new GenreMusicalManager();
$extraitManager = new ExtraitManager();

$idGenre = null;
$idExtrait = null;
$idSerie = null;

try {
    // 1. Créer un genre musical pour le test
    $genre = new GenreMusical(null, 'Genre Test Serie');
    $idGenre = $genreManager->addGenreMusical($genre);

    // 2. Créer un extrait musical pour le test
    $extrait = new Extrait(
        null,
        'Musique Test Serie',
        'Artiste Test',
        30,
        '/audio/test-serie.mp3',
        '#FF5733',
        $idGenre
    );

    $idExtrait = $extraitManager->addExtrait($extrait);

    // 3. Créer une série
    $serie = new Serie(null, 'Serie Test');
    $idSerie = $serieManager->addSerie($serie);

    $serieRelue = $serieManager->getSerieById($idSerie);

    if ($serieRelue === null || $serieRelue->getNom() !== 'Serie Test') {
        throw new Exception('Erreur lors de la création de la série.');
    }

    echo "<p>✅ Série créée et récupérée.</p>";

    // 4. Modifier la série
    $serieRelue->setNom('Serie Modifiee');
    $serieManager->updateSerie($serieRelue);

    $serieModifiee = $serieManager->getSerieById($idSerie);

    if ($serieModifiee === null || $serieModifiee->getNom() !== 'Serie Modifiee') {
        throw new Exception('Erreur lors de la modification.');
    }

    echo "<p>✅ Série modifiée.</p>";

    // 5. Vérifier la liste des séries
    $series = $serieManager->getSeries();
    $trouve = false;

    foreach ($series as $element) {
        if ($element->getId() === $idSerie) {
            $trouve = true;
            break;
        }
    }

    if (!$trouve) {
        throw new Exception('Série absente de la liste.');
    }

    echo "<p>✅ Liste des séries récupérée.</p>";

    // 6. Associer l'extrait à la série
    $serieManager->addExtraitToSerie($idSerie, $idExtrait, 1);

    $extraits = $serieManager->getExtraitsBySerie($idSerie);

    if (
        count($extraits) !== 1 ||
        $extraits[0]->getId() !== $idExtrait
    ) {
        throw new Exception('Erreur lors de l’association de l’extrait.');
    }

    echo "<p>✅ Extrait ajouté à la série et récupéré.</p>";

    // 7. Retirer l'extrait de la série
    $serieManager->removeExtraitFromSerie($idSerie, $idExtrait);

    if (count($serieManager->getExtraitsBySerie($idSerie)) !== 0) {
        throw new Exception('Erreur lors du retrait de l’extrait.');
    }

    echo "<p>✅ Extrait retiré de la série.</p>";

    // 8. Supprimer la série
    $serieManager->removeSerie($idSerie);

    if ($serieManager->getSerieById($idSerie) !== null) {
        throw new Exception('Erreur lors de la suppression de la série.');
    }

    $idSerie = null;

    echo "<p>✅ Série supprimée.</p>";
    echo "<p>🎉 Tous les tests Serie ont réussi !</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Nettoyer les données créées pour le test
    if ($idSerie !== null) {
        $serieManager->removeSerie($idSerie);
    }

    if ($idExtrait !== null) {
        $extraitManager->removeExtrait($idExtrait);
    }

    if ($idGenre !== null) {
        $genreManager->removeGenreMusical($idGenre);
    }
}
