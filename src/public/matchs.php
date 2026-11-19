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

use Comptes\CompteManager;
use Profils\ProfilManager;
use Matchs\MatchDuoManager;
use Partages\PartageInsta;
use Partages\PartageInstaManager;

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}

$compteManager = new CompteManager();
$profilManager = new ProfilManager();
$matchManager = new MatchDuoManager();
$partageManager = new PartageInstaManager();

$monProfil = $profilManager->getProfilByCompteId($_SESSION['id_compte']);

if ($monProfil === null) {
    header('Location: creer-profil.php');
    exit();
}

// Bouton « + Partager le mien » / « Partagé ✓ » : on partage ou on retire le partage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['partager'])) {
    $idAutreProfil = (int) $_POST['partager'];

    // Sécurité : on ne peut partager qu'avec quelqu'un avec qui on a un match
    if ($matchManager->getMatchByProfils($monProfil->getId(), $idAutreProfil) !== null) {
        if ($partageManager->aPartage($monProfil->getId(), $idAutreProfil)) {
            $partageManager->removePartage($monProfil->getId(), $idAutreProfil);
        } else {
            $partageManager->addPartage(new PartageInsta($monProfil->getId(), $idAutreProfil, new DateTime()));
        }
    }

    // On recharge la page en gardant le filtre choisi
    $filtreActuel = $_POST['filtre'] ?? 'tous';
    header('Location: matchs.php?filtre=' . urlencode($filtreActuel));
    exit();
}

// Filtre choisi : tous, recus (ils m'ont partagé leur insta) ou echanges (insta échangé)
$filtre = $_GET['filtre'] ?? 'tous';
if ($filtre !== 'recus' && $filtre !== 'echanges') {
    $filtre = 'tous';
}

// On prépare toutes les cartes, et on compte pour chaque filtre
$cartes = [];
$nbTous = 0;
$nbRecus = 0;
$nbEchanges = 0;

foreach ($matchManager->getMatchsByProfilId($monProfil->getId()) as $match) {
    $autreProfil = $profilManager->getProfilById($match->getIdAutreProfil($monProfil->getId()));
    $autreCompte = $compteManager->getCompteById($autreProfil->getIdCompte());

    $ilMaPartage = $partageManager->aPartage($autreProfil->getId(), $monProfil->getId());
    $jaiPartage = $partageManager->aPartage($monProfil->getId(), $autreProfil->getId());

    // État du partage d'Instagram (pour le badge)
    if ($ilMaPartage && $jaiPartage) {
        $etat = 'echange';
    } else if ($ilMaPartage) {
        $etat = 'recu';
    } else if ($jaiPartage) {
        $etat = 'attente';
    } else {
        $etat = 'aucun';
    }

    $nbTous++;
    if ($ilMaPartage) {
        $nbRecus++;
    }
    if ($etat === 'echange') {
        $nbEchanges++;
    }

    // On garde la carte seulement si elle correspond au filtre
    if ($filtre === 'recus' && !$ilMaPartage) {
        continue;
    }
    if ($filtre === 'echanges' && $etat !== 'echange') {
        continue;
    }

    $cartes[] = [
        'id_match' => $match->getId(),
        'id_profil' => $autreProfil->getId(),
        'pseudo' => $autreCompte->getPseudo(),
        'age' => $autreProfil->getAge(),
        'ville' => $autreProfil->getVille(),
        'photo' => $autreProfil->getPhotoProfil(),
        'score' => $match->getScoreGlobal(),
        'sons_communs' => $match->getNbSonsCommuns(),
        'instagram' => $autreProfil->getNomInstagram(),
        'il_ma_partage' => $ilMaPartage,
        'jai_partage' => $jaiPartage,
        'etat' => $etat,
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langue) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($textes['matchs_titre']) ?> | SoundMatch</title>
</head>

<body>
    <?php require __DIR__ . '/../includes/entete.php'; ?>

    <main>
        <h1><?= htmlspecialchars($textes['matchs_titre']) ?></h1>
        <a href="session.php"><?= htmlspecialchars($textes['session_nouvelle']) ?></a>

        <!-- Filtres -->
        <nav>
            <a href="matchs.php?filtre=tous"><?= htmlspecialchars($textes['matchs_filtre_tous']) ?> (<?= htmlspecialchars($nbTous) ?>)</a>
            <a href="matchs.php?filtre=recus"><?= htmlspecialchars($textes['matchs_filtre_recus']) ?> (<?= htmlspecialchars($nbRecus) ?>)</a>
            <a href="matchs.php?filtre=echanges"><?= htmlspecialchars($textes['matchs_filtre_echanges']) ?> (<?= htmlspecialchars($nbEchanges) ?>)</a>
        </nav>

        <?php if ($nbTous === 0) { ?>
            <p><?= htmlspecialchars($textes['accueil_aucun_match']) ?></p>
        <?php } else if (empty($cartes)) { ?>
            <p><?= htmlspecialchars($textes['matchs_aucun_filtre']) ?></p>
        <?php } ?>

        <!-- Une carte par match, de la plus compatible à la moins compatible -->
        <?php foreach ($cartes as $carte) { ?>
            <article>
                <img src="<?= htmlspecialchars($carte['photo']) ?>" alt="<?= htmlspecialchars($carte['pseudo']) ?>" width="80">
                <h2>
                    <?= htmlspecialchars($carte['pseudo']) ?>,<?= htmlspecialchars($carte['age']) ?>
                    <?php if ($carte['ville'] !== null) { ?>· <?= htmlspecialchars($carte['ville']) ?><?php } ?>
                </h2>
                <p><?= htmlspecialchars($carte['score']) ?> %<?= htmlspecialchars($textes['matchs_compatibilite']) ?></p>
                <p><?= htmlspecialchars($carte['sons_communs']) ?><?= htmlspecialchars($textes['matchs_sons_communs']) ?></p>

                <!-- Badge Instagram -->
                <p><?= htmlspecialchars($textes['matchs_etat_' . $carte['etat']]) ?></p>

                <!-- Lui/elle → moi -->
                <p>
                    <?= htmlspecialchars($carte['pseudo']) ?> →<?= htmlspecialchars($textes['matchs_toi']) ?> :
                    <?php if ($carte['il_ma_partage']) { ?>
                        <a href="https://instagram.com/<?= htmlspecialchars($carte['instagram']) ?>" target="_blank" rel="noopener">@<?= htmlspecialchars($carte['instagram']) ?> ↗</a>
                    <?php } else { ?>
                        <?= htmlspecialchars($textes['matchs_pas_partage']) ?>
                    <?php } ?>
                </p>

                <!-- Moi → lui/elle -->
                <form method="post">
                    <?= htmlspecialchars($textes['matchs_toi']) ?> → <?= htmlspecialchars($carte['pseudo']) ?> :
                    <input type="hidden" name="filtre" value="<?= htmlspecialchars($filtre) ?>">
                    <button type="submit" name="partager" value="<?= htmlspecialchars($carte['id_profil']) ?>">
                        <?php if ($carte['jai_partage']) { ?><?= htmlspecialchars($textes['matchs_partage_ok']) ?><?php } else { ?><?= htmlspecialchars($textes['matchs_partager']) ?><?php } ?>
                    </button>
                </form>

                <a href="compatibilite.php?id=<?= htmlspecialchars($carte['id_match']) ?>"><?= htmlspecialchars($textes['matchs_details']) ?></a>
            </article>
        <?php } ?>
    </main>

    <?php if (!$cookiesAcceptes) { ?>
        <p>
            <?= htmlspecialchars($textes['cookies_message']) ?>
            <a href="?cookies=ok"><?= htmlspecialchars($textes['cookies_ok']) ?></a>
        </p>
    <?php } ?>
</body>

</html>
