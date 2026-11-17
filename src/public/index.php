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
use Matchs\MatchDuoManager;
use Comptes\CompteManager;

// Est-ce que quelqu'un est connecté ?
$estConnecte = isset($_SESSION['id_compte']);

// Les 3 meilleurs matchs (seulement si la personne est connectée)
$meilleursMatchs = [];

if ($estConnecte) {
    $profilManager = new ProfilManager();
    $matchManager = new MatchDuoManager();
    $compteManager = new CompteManager();

    // On cherche le profil de la personne connectée
    // (il n'existe pas encore si elle n'a pas répondu aux questions du premier lancement)
    $monProfil = $profilManager->getProfilByCompteId($_SESSION['id_compte']);

    if ($monProfil !== null) {
        // Les matchs sont déjà triés du plus compatible au moins compatible
        $matchs = $matchManager->getMatchsByProfilId($monProfil->getId());

        foreach ($matchs as $match) {
            // On s'arrête quand on en a 3
            if (count($meilleursMatchs) === 3) {
                break;
            }

            // On retrouve l'autre personne du match, puis son pseudo
            $autreProfil = $profilManager->getProfilById($match->getIdAutreProfil($monProfil->getId()));
            $autreCompte = $compteManager->getCompteById($autreProfil->getIdCompte());

            $meilleursMatchs[] = [
                'id_match' => $match->getId(),
                'pseudo' => $autreCompte->getPseudo(),
                'age' => $autreProfil->getAge(),
                'score' => $match->getScoreGlobal(),
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundMatch</title>
</head>

<body>
    <?php if ($estConnecte) { ?>
        <!-- Menu des pages connectées -->
        <?php require __DIR__ . '/../includes/entete.php'; ?>
    <?php } else { ?>
        <!-- Visiteur : choix de la langue + liens vers le compte -->
        <header>
            <a href="index.php">SoundMatch</a>
            <a href="?langue=fr"><?= $textes['francais'] ?></a> | <a href="?langue=en"><?= $textes['anglais'] ?></a>
            <a href="connexion.php"><?= $textes['accueil_se_connecter'] ?></a>
            <a href="inscription.php"><?= $textes['accueil_creer_compte'] ?></a>
        </header>
    <?php } ?>

    <main>
        <!-- Accroche -->
        <section>
            <h1><?= $textes['accueil_accroche'] ?></h1>
            <p><?= $textes['accueil_texte'] ?></p>

            <?php if ($estConnecte) { ?>
                <p><?= $textes['profil_bonjour'] ?> <?= htmlspecialchars($_SESSION['pseudo']) ?> 👋</p>
                <a href="session.php"><?= $textes['accueil_commencer'] ?></a>
            <?php } else { ?>
                <a href="inscription.php"><?= $textes['accueil_creer_compte'] ?></a>
            <?php } ?>

            <a href="#comment"><?= $textes['menu_comment'] ?></a>
        </section>

        <!-- Le concept : 3 étapes -->
        <section id="concept">
            <h2><?= $textes['menu_concept'] ?></h2>
            <ol>
                <li><strong><?= $textes['concept_ecoute_titre'] ?></strong> : <?= $textes['concept_ecoute_texte'] ?></li>
                <li><strong><?= $textes['concept_choisis_titre'] ?></strong> : <?= $textes['concept_choisis_texte'] ?></li>
                <li><strong><?= $textes['concept_match_titre'] ?></strong> : <?= $textes['concept_match_texte'] ?></li>
            </ol>
        </section>

        <!-- Comment ça marche ? -->
        <section id="comment">
            <h2><?= $textes['menu_comment'] ?></h2>
            <p><?= $textes['comment_texte'] ?></p>
            <p><?= $textes['comment_exemple'] ?></p>
        </section>

        <!-- Tes meilleurs matchs (seulement si connecté) -->
        <?php if ($estConnecte) { ?>
            <section>
                <h2><?= $textes['accueil_meilleurs_matchs'] ?></h2>

                <?php if (empty($meilleursMatchs)) { ?>
                    <p><?= $textes['accueil_aucun_match'] ?></p>
                    <a href="session.php"><?= $textes['accueil_commencer'] ?></a>
                <?php } else { ?>
                    <ul>
                        <?php foreach ($meilleursMatchs as $unMatch) { ?>
                            <li>
                                <a href="compatibilite.php?id=<?= $unMatch['id_match'] ?>">
                                    <?= htmlspecialchars($unMatch['pseudo']) ?>,
                                    <?= $unMatch['age'] ?> <?= $textes['accueil_ans'] ?> :
                                    <?= $unMatch['score'] ?> %
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <a href="matchs.php"><?= $textes['accueil_voir_matchs'] ?></a>
                <?php } ?>
            </section>
        <?php } ?>
    </main>

    <footer>
        <p><?= $textes['accueil_description'] ?></p>
    </footer>

    <?php if (!$cookiesAcceptes) { ?>
        <p>
            <?= $textes['cookies_message'] ?>
            <a href="?cookies=ok"><?= $textes['cookies_ok'] ?></a>
        </p>
    <?php } ?>
</body>

</html>
