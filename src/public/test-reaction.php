<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Profils\Profil;
use Profils\ProfilManager;
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

$compteManager = new CompteManager();
$profilManager = new ProfilManager();
$reactionManager = new ReactionManager();
$sessionManager = new SessionEcouteManager();
$serieManager = new SerieManager();
$extraitManager = new ExtraitManager();
$genreManager = new GenreMusicalManager();

$idCompte = null;
$idProfil = null;
$idGenre = null;
$idExtrait = null;
$idSerie = null;
$idSession = null;

try {
    // 0. Une session appartient à un profil : on crée un compte et un profil de test
    //    (au lieu d'utiliser un id écrit en dur, qui peut ne plus exister)
    $idCompte = $compteManager->addCompte(new Compte(
        null, 'test_reaction', 'test.reaction@example.com',
        password_hash('motdepasse123', PASSWORD_DEFAULT), 'fr', false,
        new DateTime(), new DateTime()
    ));

    $idProfil = $profilManager->addProfil(new Profil(
        null, 'femme', new DateTime('2002-03-01'), 'uploads/test.jpg',
        'test.reaction', null, $idCompte
    ));

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

    // On supprime d'abord le profil (il dépend du compte), puis le compte
    if ($idProfil !== null) {
        $profilManager->removeProfil($idProfil);
    }

    if ($idCompte !== null) {
        $compteManager->removeCompte($idCompte);
    }
}
