<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Sessions\SessionEcoute;
use Sessions\SessionEcouteManager;
use Series\Serie;
use Series\SerieManager;

$sessionManager = new SessionEcouteManager();
$serieManager = new SerieManager();

$idSerie = null;
$idSession = null;

// À remplacer par l'identifiant d'un profil de test existant
$idProfil = 1;

try {
    // Vérifier que le profil de test existe
    $pdo = (new Database())->getPdo();

    $stmt = $pdo->prepare(
        "SELECT id_profil FROM profil WHERE id_profil = :id"
    );
    $stmt->execute([':id' => $idProfil]);

    if (!$stmt->fetch()) {
        throw new Exception(
            "Le profil de test n'existe pas. Vérifie idProfil."
        );
    }

    // 1. Créer une série temporaire
    $serie = new Serie(null, 'Serie Test Session');
    $idSerie = $serieManager->addSerie($serie);

    // 2. Créer une session d'écoute
    $session = new SessionEcoute(
        null,
        new DateTimeImmutable(),
        'en_cours',
        $idProfil,
        $idSerie
    );

    $idSession = $sessionManager->addSessionEcoute($session);

    // 3. Récupérer la session
    $sessionRelue = $sessionManager->getSessionEcouteById($idSession);

    if (
        $sessionRelue === null ||
        $sessionRelue->getIdProfil() !== $idProfil ||
        $sessionRelue->getIdSerie() !== $idSerie
    ) {
        throw new Exception('Erreur lors de la lecture.');
    }

    echo "<p>✅ Session créée et récupérée.</p>";

    // 4. Modifier le statut
    $sessionRelue->setStatut('terminee');
    $sessionManager->updateSessionEcoute($sessionRelue);

    $sessionModifiee = $sessionManager->getSessionEcouteById($idSession);

    if (
        $sessionModifiee === null ||
        $sessionModifiee->getStatut() !== 'terminee'
    ) {
        throw new Exception('Erreur lors de la modification.');
    }

    echo "<p>✅ Statut de la session modifié.</p>";

    // 5. Récupérer toutes les sessions
    $sessions = $sessionManager->getSessionsEcoute();
    $trouve = false;

    foreach ($sessions as $element) {
        if ($element->getId() === $idSession) {
            $trouve = true;
            break;
        }
    }

    if (!$trouve) {
        throw new Exception('Session absente de la liste.');
    }

    echo "<p>✅ Liste des sessions récupérée.</p>";

    // 6. Supprimer la session
    $sessionManager->removeSessionEcoute($idSession);

    if ($sessionManager->getSessionEcouteById($idSession) !== null) {
        throw new Exception('Erreur lors de la suppression.');
    }

    $idSession = null;

    echo "<p>✅ Session supprimée.</p>";
    echo "<p>🎉 Tous les tests SessionEcoute ont réussi !</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " .
        htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Nettoyer les données temporaires
    if ($idSession !== null) {
        $sessionManager->removeSessionEcoute($idSession);
    }

    if ($idSerie !== null) {
        $serieManager->removeSerie($idSerie);
    }
}
