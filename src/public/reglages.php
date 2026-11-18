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
use Profils\Profil;
use Profils\ProfilManager;
use Partages\PartageInstaManager;

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}

$compteManager = new CompteManager();
$profilManager = new ProfilManager();
$partageManager = new PartageInstaManager();

// Le compte et le profil de la personne connectée
$compte = $compteManager->getCompteById($_SESSION['id_compte']);
$profil = $profilManager->getProfilByCompteId($_SESSION['id_compte']);

// Pas encore de profil : il faut d'abord répondre aux questions
if ($profil === null) {
    header('Location: creer-profil.php');
    exit();
}

// Photo : 5 Mo maximum, seulement JPG ou PNG
const TAILLE_PHOTO_MAXIMUM = 5 * 1024 * 1024;
const EXTENSIONS_PHOTO = ['jpg', 'jpeg', 'png'];

// Valeurs affichées dans le formulaire : au départ, celles de la base
$pseudo = $compte->getPseudo();
$dateNaissance = $profil->getDateNaissance()->format('Y-m-d');
$genre = $profil->getGenre();
$instagram = $profil->getNomInstagram();

$erreurs = [];
$succes = false;

// A. Bouton « Ne plus partager » : on retire un partage d'Instagram
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer_partage'])) {
    $partageManager->removePartage($profil->getId(), (int) $_POST['retirer_partage']);
    $succes = true;
}

// B. Bouton « Enregistrer » : on modifie le profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer'])) {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $dateNaissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $instagram = trim($_POST['instagram'] ?? '');
    // On enlève le @ au début s'il y en a un
    $instagram = ltrim($instagram, '@');

    // 1. Pseudo : mêmes règles qu'à l'inscription
    if (!preg_match('/^[A-Za-z0-9._]{3,20}$/', $pseudo)) {
        $erreurs['pseudo'] = $textes['erreur_pseudo'];
    } else {
        // Le pseudo ne doit pas déjà être pris par QUELQU'UN D'AUTRE
        $autreCompte = $compteManager->getCompteByPseudo($pseudo);
        if ($autreCompte !== null && $autreCompte->getId() !== $compte->getId()) {
            $erreurs['pseudo'] = $textes['erreur_pseudo_utilise'];
        }
    }

    // 2. Date de naissance : au bon format, et 18 ans ou plus
    $date = DateTime::createFromFormat('Y-m-d', $dateNaissance);

    if ($date === false || $date > new DateTime()) {
        $erreurs['date_naissance'] = $textes['erreur_date_naissance'];
    } else if ($date->diff(new DateTime())->y < Profil::AGE_MINIMUM) {
        $erreurs['date_naissance'] = $textes['erreur_age'];
    }

    // 3. Genre : une des 3 réponses possibles
    if (!in_array($genre, Profil::GENRES)) {
        $erreurs['genre'] = $textes['erreur_genre'];
    }

    // 4. Instagram : 2 à 30 caractères (lettres, chiffres, point ou tiret bas)
    if (!preg_match('/^[A-Za-z0-9._]{2,30}$/', $instagram)) {
        $erreurs['instagram'] = $textes['erreur_instagram'];
    }

    // 5. Photo : FACULTATIVE ici (on garde l'ancienne si on n'en choisit pas)
    $photo = $_FILES['photo'] ?? null;
    $nouvellePhoto = false;

    if ($photo !== null && $photo['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($photo['error'] !== UPLOAD_ERR_OK) {
            $erreurs['photo'] = $textes['erreur_photo'];
        } else {
            $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            
            if ($photo['size'] > TAILLE_PHOTO_MAXIMUM) {
                $erreurs['photo'] = $textes['erreur_photo_taille'];
            } else if (!in_array($extension, EXTENSIONS_PHOTO) || getimagesize($photo['tmp_name']) === false) {
                $erreurs['photo'] = $textes['erreur_photo_format'];
            } else {
                $nouvellePhoto = true;
            }
        }
    }

    // 6. Tout est bon : on enregistre
    if (empty($erreurs)) {
        try {
            // Nouvelle photo : on l'enregistre et on supprime l'ancienne
            if ($nouvellePhoto) {
                $dossierPhotos = __DIR__ . '/uploads/photos';
                if (!is_dir($dossierPhotos)) {
                    mkdir($dossierPhotos, 0755, true);
                }

                $nomPhoto = uniqid('photo_') . '.' . $extension;
                move_uploaded_file($photo['tmp_name'], $dossierPhotos . '/' . $nomPhoto);

                $anciennePhoto = __DIR__ . '/' . $profil->getPhotoProfil();
                if (is_file($anciennePhoto)) {
                    unlink($anciennePhoto);
                }

                $profil->setPhotoProfil('uploads/photos/' . $nomPhoto);
            }

            // Le profil (les setters revérifient les données)
            $profil->setDateNaissance($date);
            $profil->setGenre($genre);
            $profil->setNomInstagram($instagram);
            $profilManager->updateProfil($profil);

            // Le pseudo est dans la table compte
            $compte->setPseudo($pseudo);
            $compteManager->updateCompte($compte);

            // On met aussi à jour le pseudo dans la session
            $_SESSION['pseudo'] = $pseudo;

            $succes = true;
        } catch (InvalidArgumentException $e) {
            $erreurs['general'] = $e->getMessage();
        }
    }
}

// Les personnes à qui j'ai partagé mon Instagram (avec leur pseudo)
$partages = [];

foreach ($partageManager->getPartagesEnvoyes($profil->getId()) as $partage) {
    $autreProfil = $profilManager->getProfilById($partage->getIdProfilDestinataire());
    $autreCompteDuPartage = $compteManager->getCompteById($autreProfil->getIdCompte());

    $partages[] = [
        'id_profil' => $autreProfil->getId(),
        'pseudo' => $autreCompteDuPartage->getPseudo(),
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $textes['reglages_titre'] ?> | SoundMatch</title>
</head>

<body>
    <?php require __DIR__ . '/../includes/entete.php'; ?>

    <main>
        <a href="index.php"><?= $textes['reglages_retour'] ?></a>
        <h1><?= $textes['reglages_titre'] ?></h1>

        <?php if ($succes) { ?>
            <p><?= $textes['reglages_succes'] ?></p>
        <?php } ?>

        <?php if (isset($erreurs['general'])) { ?>
            <p><?= htmlspecialchars($erreurs['general']) ?></p>
        <?php } ?>

        <!-- Menu de la page -->
        <nav>
            <a href="#profil"><?= $textes['reglages_mon_profil'] ?></a>
            <a href="#instagram"><?= $textes['reglages_instagram'] ?></a>
            <a href="#compte"><?= $textes['reglages_compte'] ?></a>
            <a href="#supprimer"><?= $textes['reglages_supprimer'] ?></a>
        </nav>

        <form method="post" enctype="multipart/form-data">
            <!-- Mon profil -->
            <section id="profil">
                <h2><?= $textes['reglages_mon_profil'] ?></h2>

                <div>
                    <!-- htmlspecialchars aussi sur le chemin de la photo -->
                    <img src="<?= htmlspecialchars($profil->getPhotoProfil()) ?>" alt="<?= $textes['reglages_photo'] ?>" width="100">
                    <label for="photo"><?= $textes['reglages_changer_photo'] ?></label>
                    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png">
                    <p><?= $textes['reglages_photo_aide'] ?></p>
                    <?php if (isset($erreurs['photo'])) { ?>
                        <p><?= htmlspecialchars($erreurs['photo']) ?></p>
                    <?php } ?>
                </div>

                <div>
                    <label for="pseudo"><?= $textes['inscription_pseudo'] ?></label>
                    <input type="text" id="pseudo" name="pseudo" required minlength="3" maxlength="20"
                        value="<?= htmlspecialchars($pseudo) ?>">
                    <?php if (isset($erreurs['pseudo'])) { ?>
                        <p><?= htmlspecialchars($erreurs['pseudo']) ?></p>
                    <?php } ?>
                </div>

                <div>
                    <label for="date_naissance"><?= $textes['creer_profil_date'] ?></label>
                    <input type="date" id="date_naissance" name="date_naissance" required
                        value="<?= htmlspecialchars($dateNaissance) ?>">
                    <?php if (isset($erreurs['date_naissance'])) { ?>
                        <p><?= htmlspecialchars($erreurs['date_naissance']) ?></p>
                    <?php } ?>
                </div>

                <fieldset>
                    <legend><?= $textes['creer_profil_genre'] ?></legend>
                    <label>
                        <input type="radio" name="genre" value="femme" required <?php if ($genre === 'femme') { ?>checked<?php } ?>>
                        <?= $textes['genre_femme'] ?>
                    </label>
                    <label>
                        <input type="radio" name="genre" value="homme" <?php if ($genre === 'homme') { ?>checked<?php } ?>>
                        <?= $textes['genre_homme'] ?>
                    </label>
                    <label>
                        <input type="radio" name="genre" value="autre" <?php if ($genre === 'autre') { ?>checked<?php } ?>>
                        <?= $textes['genre_autre'] ?>
                    </label>
                    <?php if (isset($erreurs['genre'])) { ?>
                        <p><?= htmlspecialchars($erreurs['genre']) ?></p>
                    <?php } ?>
                </fieldset>
            </section>

            <!-- Instagram -->
            <section id="instagram">
                <h2><?= $textes['reglages_instagram'] ?></h2>
                <label for="instagram"><?= $textes['creer_profil_instagram'] ?></label>
                @<input type="text" id="instagram" name="instagram" required minlength="2" maxlength="31"
                    value="<?= htmlspecialchars($instagram) ?>">
                <?php if (isset($erreurs['instagram'])) { ?>
                    <p><?= htmlspecialchars($erreurs['instagram']) ?></p>
                <?php } ?>
            </section>

            <button type="submit" name="enregistrer"><?= $textes['reglages_enregistrer'] ?></button>
        </form>

        <!-- Partagé avec : un petit formulaire par personne (en dehors du grand formulaire) -->
        <section>
            <h3><?= $textes['reglages_partage_avec'] ?></h3>

            <?php if (empty($partages)) { ?>
                <p><?= $textes['reglages_aucun_partage'] ?></p>
            <?php } else { ?>
                <ul>
                    <?php foreach ($partages as $unPartage) { ?>
                        <li>
                            <?= htmlspecialchars($unPartage['pseudo']) ?>
                            <form method="post">
                                <button type="submit" name="retirer_partage" value="<?= $unPartage['id_profil'] ?>">
                                    <?= $textes['reglages_ne_plus_partager'] ?>
                                </button>
                            </form>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </section>

        <!-- Compte et sécurité -->
        <section id="compte">
            <h2><?= $textes['reglages_compte'] ?></h2>
            <p><?= $textes['inscription_email'] ?> : <?= htmlspecialchars($compte->getEmail()) ?></p>
            <p><?= $textes['reglages_email_bientot'] ?></p>
            <a href="mot-de-passe.php"><?= $textes['reglages_modifier_mdp'] ?></a>
            <a href="appareils.php"><?= $textes['reglages_appareils'] ?></a>
        </section>

        <!-- Supprimer mon compte -->
        <section id="supprimer">
            <h2><?= $textes['reglages_supprimer'] ?></h2>
            <p><?= $textes['reglages_supprimer_texte'] ?></p>
            <a href="supprimer-compte.php"><?= $textes['reglages_supprimer'] ?></a>
        </section>
    </main>

    <?php if (!$cookiesAcceptes) { ?>
        <p>
            <?= $textes['cookies_message'] ?>
            <a href="?cookies=ok"><?= $textes['cookies_ok'] ?></a>
        </p>
    <?php } ?>
</body>

</html>
