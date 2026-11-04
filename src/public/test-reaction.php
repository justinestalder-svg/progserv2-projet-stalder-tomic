<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Reactions\Reaction;
use Reactions\ReactionManager;
use Sessions\SessionEcoute;
use Sessions\SessionEcouteManager;
use Series\Serie;
use Series\SerieManager;
use Extraits\Extrait;
use Extraits\ExtraitManager;
use Genres\GenreMusical;
use Genres\GenreMusicalManager;

$reactionManager = new ReactionManager();
$sessionManager = new SessionEcouteManager();
$serieManager = new SerieManager();
$extraitManager = new ExtraitManager();
$genreManager = new GenreMusicalManager();

$idGenre = null;
$idExtrait = null;
$idSerie = null;
$idSession = null;

// Identifiant d'un profil de test existant
$idProfil = 1;

try {
    $pdo = (new Database())->getPdo();

    // Vérifier que le profil existe
    $stmt = $pdo->prepare(
        "SELECT id_profil FROM profil WHERE id_profil = :id"
    );
    $stmt->execute([':id' => $idProfil]);

    if (!$stmt->fetch()) {
        throw new Exception(
            "Le profil de test n'existe pas. Vérifie idProfil."
        );
    }

    // 1. Créer un genre musical
    $genre = new GenreMusical(null, 'Genre Test Reaction');
    $idGenre = $genreManager->addGenreMusical($genre);

    // 2. Créer un extrait
    $extrait = new Extrait(
        null,
        'Musique Test Reaction',
        'Artiste Test',
        30,
        '/audio/test-reaction.mp3',
        '#FF5733',
        $idGenre
    );

    $idExtrait = $extraitManager->addExtrait($extrait);

    // 3. Créer une série
    $serie = new Serie(null, 'Serie Test Reaction');
    $idSerie = $serieManager->addSerie($serie);

    // Associer l'extrait à la série
    $serieManager->addExtraitToSerie($idSerie, $idExtrait, 1);

    // 4. Créer une session d'écoute
    $session = new SessionEcoute(
        null,
        new DateTimeImmutable(),
        'en_cours',
        $idProfil,
        $idSerie
    );

    $idSession = $sessionManager->addSessionEcoute($session);

    // 5. Ajouter une réaction
    $reaction = new Reaction(
        $idSession,
        $idExtrait,
        'like',
        new DateTimeImmutable()
    );

    $reactionManager->addReaction($reaction);

    $reactionRelue = $reactionManager->getReactionById(
        $idSession,
        $idExtrait
    );

    if (
        $reactionRelue === null ||
        $reactionRelue->getChoix() !== 'like'
    ) {
        throw new Exception('Erreur lors de la création de la réaction.');
    }

    echo "<p>✅ Réaction créée et récupérée.</p>";

    // 6. Modifier la réaction
    $reactionRelue->setChoix('dislike');
    $reactionManager->updateReaction($reactionRelue);

    $reactionModifiee = $reactionManager->getReactionById(
        $idSession,
        $idExtrait
    );

    if (
        $reactionModifiee === null ||
        $reactionModifiee->getChoix() !== 'dislike'
    ) {
        throw new Exception('Erreur lors de la modification.');
    }

    echo "<p>✅ Réaction modifiée.</p>";

    // 7. Vérifier la liste des réactions
    $reactions = $reactionManager->getReactions();
    $trouve = false;

    foreach ($reactions as $element) {
        if (
            $element->getIdSession() === $idSession &&
            $element->getIdExtrait() === $idExtrait
        ) {
            $trouve = true;
            break;
        }
    }

    if (!$trouve) {
        throw new Exception('Réaction absente de la liste.');
    }

    echo "<p>✅ Liste des réactions récupérée.</p>";

    // 8. Supprimer la réaction
    $reactionManager->removeReaction($idSession, $idExtrait);

    if (
        $reactionManager->getReactionById($idSession, $idExtrait) !== null
    ) {
        throw new Exception('Erreur lors de la suppression.');
    }

    echo "<p>✅ Réaction supprimée.</p>";
    echo "<p>🎉 Tous les tests Reaction ont réussi !</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " .
        htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Supprimer la réaction si elle existe encore
    if ($idSession !== null && $idExtrait !== null) {
        $reactionManager->removeReaction($idSession, $idExtrait);
    }

    // Supprimer les données temporaires
    if ($idSession !== null) {
        $sessionManager->removeSessionEcoute($idSession);
    }

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
