<?php
// Cookie de session protégé : le JavaScript ne peut pas le lire (httponly)
// et il n'est pas envoyé par les autres sites (samesite)
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/../utils/autoloader.php';
require_once __DIR__ . '/../utils/langue.php';

use Profils\ProfilManager;
use Series\SerieManager;
use Sessions\SessionEcoute;
use Sessions\SessionEcouteManager;
use Reactions\Reaction;
use Reactions\ReactionManager;
use Favoris\Favori;
use Favoris\FavoriManager;
use Genres\GenreMusicalManager;

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}

$profilManager = new ProfilManager();
$serieManager = new SerieManager();
$sessionManager = new SessionEcouteManager();
$reactionManager = new ReactionManager();
$favoriManager = new FavoriManager();
$genreManager = new GenreMusicalManager();

// Il faut un profil pour faire une session
$profil = $profilManager->getProfilByCompteId($_SESSION['id_compte']);

if ($profil === null) {
    header('Location: creer-profil.php');
    exit();
}

// ===== Écran de fin : après le 20e choix =====
if (isset($_GET['fin'])) {
    $sessionTerminee = $sessionManager->getSessionEcouteById((int) $_GET['fin']);

    // On vérifie que cette session est bien celle de la personne connectée
    if ($sessionTerminee === null || $sessionTerminee->getIdProfil() !== $profil->getId()) {
        header('Location: session.php');
        exit();
    }

    // On compte les « J'aime »
    $nbAimes = 0;
    $nbExtraitsSession = 0;
    foreach ($reactionManager->getReactionsBySession($sessionTerminee->getId()) as $reaction) {
        $nbExtraitsSession++;
        if ($reaction->getChoix() === 'aime') {
            $nbAimes++;
        }
    }
}

// ===== La session en cours =====
if (!isset($_GET['fin'])) {
    // On prend la première série de la base
    $series = $serieManager->getSeries();

    if (empty($series)) {
        $serie = null;
    } else {
        $serie = $series[0];

        // On reprend la session en cours, ou on en crée une nouvelle
        $session = $sessionManager->getSessionEnCoursByProfil($profil->getId());

        if ($session === null) {
            $session = new SessionEcoute(null, new DateTimeImmutable(), 'en_cours', $profil->getId(), $serie->getId());
            $session->setId($sessionManager->addSessionEcoute($session));
        }

        // Les 20 extraits de la série, dans l'ordre (rang 1 à 20)
        $extraits = $serieManager->getExtraitsBySerie($session->getIdSerie());

        // Les choix déjà faits : [id de l'extrait => 'aime' ou 'ignore']
        $choix = [];
        foreach ($reactionManager->getReactionsBySession($session->getId()) as $reaction) {
            $choix[$reaction->getIdExtrait()] = $reaction->getChoix();
        }

        // La position de l'extrait en cours = nombre de choix déjà faits
        $position = count($choix);
        $extraitEnCours = $extraits[$position];

        // ===== Les boutons (formulaires) =====
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'aime' || $action === 'ignore') {
                // On enregistre le choix pour l'extrait en cours
                $reactionManager->addReaction(new Reaction($session->getId(), $extraitEnCours->getId(), $action, new DateTimeImmutable()));

                // C'était le dernier extrait : la session est terminée
                if ($position + 1 === count($extraits)) {
                    $sessionTerminee = new SessionEcoute($session->getId(), $session->getDateDebut(), 'terminee', $session->getIdProfil(), $session->getIdSerie());
                    $sessionManager->updateSessionEcoute($sessionTerminee);

                    header('Location: session.php?fin=' . $session->getId());
                    exit();
                }
            } else if ($action === 'precedent' && $position > 0) {
                // On annule le dernier choix
                $reactionManager->removeReaction($session->getId(), $extraits[$position - 1]->getId());
            } else if ($action === 'favori') {
                // On ajoute ou on retire l'extrait en cours des favoris
                if ($favoriManager->getFavoriById($profil->getId(), $extraitEnCours->getId()) === null) {
                    $favoriManager->addFavori(new Favori($profil->getId(), $extraitEnCours->getId(), new DateTimeImmutable()));
                } else {
                    $favoriManager->removeFavori($profil->getId(), $extraitEnCours->getId());
                }
            } else if ($action === 'quitter') {
                // On abandonne la session : ses réactions ne sont pas gardées
                $reactionManager->removeReactionsBySession($session->getId());
                $sessionManager->removeSessionEcoute($session->getId());

                header('Location: index.php');
                exit();
            }

            // On recharge la page (évite de renvoyer le formulaire avec F5)
            header('Location: session.php');
            exit();
        }

        // ===== Ce qu'on affiche =====
        // Compteurs
        $nbAimes = 0;
        $nbIgnores = 0;
        foreach ($choix as $unChoix) {
            if ($unChoix === 'aime') {
                $nbAimes++;
            } else {
                $nbIgnores++;
            }
        }

        // Barre de progression : un symbole par extrait
        // ✓ = aimé, ✕ = ignoré, ● = en cours, · = à venir
        $barre = [];
        foreach ($extraits as $numero => $unExtrait) {
            if (isset($choix[$unExtrait->getId()])) {
                if ($choix[$unExtrait->getId()] === 'aime') {
                    $barre[] = '✓';
                } else {
                    $barre[] = '✕';
                }
            } else if ($numero === $position) {
                $barre[] = '●';
            } else {
                $barre[] = '·';
            }
        }

        // Le nom du genre de l'extrait en cours
        $genre = $genreManager->getGenreMusicalById($extraitEnCours->getIdGenre());

        // Couleur de la pochette (beige si l'extrait n'en a pas)
        $couleurPochette = $extraitEnCours->getCouleurPochette();
        if ($couleurPochette === null) {
            $couleurPochette = '#E3DACC';
        }

        // Est-ce que l'extrait en cours est dans les favoris ?
        $estFavori = $favoriManager->getFavoriById($profil->getId(), $extraitEnCours->getId()) !== null;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $textes['session_titre'] ?> | SoundMatch</title>
</head>

<body>
    <?php require __DIR__ . '/../includes/entete.php'; ?>

    <main>
        <?php if (isset($_GET['fin'])) { ?>
            <!-- Écran de fin -->
            <h1><?= $textes['session_terminee'] ?></h1>
            <p><?= $textes['session_resultat_debut'] ?> <?= $nbAimes ?> <?= $textes['session_resultat_fin'] ?> <?= $nbExtraitsSession ?>.</p>
            <a href="session.php"><?= $textes['session_nouvelle'] ?></a>
            <a href="matchs.php"><?= $textes['session_voir_matchs'] ?></a>

        <?php } else if ($serie === null) { ?>
            <!-- Pas encore de musiques dans la base -->
            <h1><?= $textes['session_titre'] ?></h1>
            <p><?= $textes['session_aucune_serie'] ?></p>

        <?php } else if (isset($_GET['quitter'])) { ?>
            <!-- Fenêtre « Quitter la session » -->
            <h1><?= $textes['session_quitter_titre'] ?></h1>
            <p><?= $textes['session_quitter_texte'] ?></p>
            <form method="post">
                <button type="submit" name="action" value="quitter"><?= $textes['session_quitter_bouton'] ?></button>
            </form>
            <a href="session.php"><?= $textes['session_continuer'] ?></a>

        <?php } else { ?>
            <!-- La session en cours -->
            <h1><?= $textes['session_titre'] ?></h1>
            <a href="session.php?quitter=1">✕ <?= $textes['session_quitter_lien'] ?></a>

            <p><?= $textes['session_extrait'] ?> <?= $position + 1 ?> / <?= count($extraits) ?></p>
            <p>✓ <?= $nbAimes ?> <?= $textes['session_aimes'] ?> · ✕ <?= $nbIgnores ?> <?= $textes['session_ignores'] ?></p>

            <!-- Barre de progression : un symbole par extrait -->
            <p>
                <?php foreach ($barre as $symbole) { ?>
                    <?= $symbole ?>
                <?php } ?>
            </p>

            <!-- Carte de l'extrait -->
            <section>
                <div style="width: 120px; height: 120px; background-color: <?= htmlspecialchars($couleurPochette) ?>;"></div>
                <h2><?= htmlspecialchars($extraitEnCours->getTitre()) ?></h2>
                <p><?= htmlspecialchars($extraitEnCours->getArtiste()) ?> · <?= htmlspecialchars($genre->getLibelle()) ?></p>

                <!-- Lecteur audio : démarre tout seul (autoplay) et recommence à la fin (loop) -->
                <audio src="<?= htmlspecialchars($extraitEnCours->getUrlAudio()) ?>" controls autoplay loop></audio>

                <form method="post">
                    <button type="submit" name="action" value="favori">
                        <?php if ($estFavori) { ?>★ <?= $textes['session_retirer_favori'] ?><?php } else { ?>☆ <?= $textes['session_ajouter_favori'] ?><?php } ?>
                    </button>
                </form>
            </section>

            <!-- J'ignore / J'aime -->
            <form method="post">
                <button type="submit" name="action" value="ignore">✕ <?= $textes['session_ignore'] ?></button>
                <button type="submit" name="action" value="aime">✓ <?= $textes['session_aime'] ?></button>
            </form>

            <?php if ($position > 0) { ?>
                <form method="post">
                    <button type="submit" name="action" value="precedent"><?= $textes['session_precedent'] ?></button>
                </form>
            <?php } ?>
        <?php } ?>
    </main>

    <?php if (!$cookiesAcceptes) { ?>
        <p>
            <?= $textes['cookies_message'] ?>
            <a href="?cookies=ok"><?= $textes['cookies_ok'] ?></a>
        </p>
    <?php } ?>
</body>

</html>
