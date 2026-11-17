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

use Profils\Profil;
use Profils\ProfilManager;

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}

$profilManager = new ProfilManager();

// Si la personne a déjà un profil, elle n'a rien à faire ici
if ($profilManager->getProfilByCompteId($_SESSION['id_compte']) !== null) {
    header('Location: index.php');
    exit();
}

// Photo : 5 Mo maximum, seulement JPG ou PNG
const TAILLE_PHOTO_MAXIMUM = 5 * 1024 * 1024;
const EXTENSIONS_PHOTO = ['jpg', 'jpeg', 'png'];

// Valeurs du formulaire (vides au premier affichage) et messages d'erreur par champ
$genre = '';
$dateNaissance = '';
$instagram = '';
$erreurs = [];

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $genre = $_POST['genre'] ?? '';
    $dateNaissance = $_POST['date_naissance'] ?? '';
    $instagram = trim($_POST['instagram'] ?? '');

    // 1. Genre : une des 3 réponses possibles
    if (!in_array($genre, Profil::GENRES)) {
        $erreurs['genre'] = $textes['erreur_genre'];
    }

    // 2. Date de naissance : au bon format, et 18 ans ou plus
    // (le champ type="date" envoie la date au format aaaa-mm-jj)
    $date = DateTime::createFromFormat('Y-m-d', $dateNaissance);

    if ($date === false || $date > new DateTime()) {
        $erreurs['date_naissance'] = $textes['erreur_date_naissance'];
    } else if ($date->diff(new DateTime())->y < Profil::AGE_MINIMUM) {
        $erreurs['date_naissance'] = $textes['erreur_age'];
    }

    // 3. Photo : bien envoyée, pas trop lourde, et une vraie image JPG ou PNG
    $photo = $_FILES['photo'] ?? null;

    if ($photo === null || $photo['error'] !== UPLOAD_ERR_OK) {
        $erreurs['photo'] = $textes['erreur_photo'];
    } else {
        // L'extension du fichier, en minuscules (ex. "jpg")
        $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

        if ($photo['size'] > TAILLE_PHOTO_MAXIMUM) {
            $erreurs['photo'] = $textes['erreur_photo_taille'];
        } else if (!in_array($extension, EXTENSIONS_PHOTO) || getimagesize($photo['tmp_name']) === false) {
            // getimagesize vérifie que c'est vraiment une image (pas un fichier renommé en .jpg)
            $erreurs['photo'] = $textes['erreur_photo_format'];
        }
    }

    // 4. Instagram : on enlève le @ du début, puis 2 à 30 caractères
    //    (lettres, chiffres, point ou tiret bas, comme sur Instagram)
    $instagram = ltrim($instagram, '@');

    if (!preg_match('/^[A-Za-z0-9._]{2,30}$/', $instagram)) {
        $erreurs['instagram'] = $textes['erreur_instagram'];
    }

    // 5. Tout est bon : on enregistre la photo, puis le profil
    if (empty($erreurs)) {
        // Dossier des photos (on le crée s'il n'existe pas encore)
        $dossierPhotos = __DIR__ . '/uploads/photos';
        if (!is_dir($dossierPhotos)) {
            mkdir($dossierPhotos, 0755, true);
        }

        // Nouveau nom unique : on ne garde jamais le nom choisi par l'utilisateur
        $nomPhoto = uniqid('photo_') . '.' . $extension;
        move_uploaded_file($photo['tmp_name'], $dossierPhotos . '/' . $nomPhoto);

        try {
            $profil = new Profil(
                null,
                $genre,
                $date,
                'uploads/photos/' . $nomPhoto,
                $instagram,
                null,
                $_SESSION['id_compte']
            );

            $profilManager->addProfil($profil);

            // Profil créé : on peut aller sur l'accueil
            header('Location: index.php');
            exit();
        } catch (InvalidArgumentException $e) {
            // Sécurité en plus : la classe Profil revérifie les données
            $erreurs['general'] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $textes['creer_profil_titre'] ?> | SoundMatch</title>
</head>

<body>
    <p>
        <a href="?langue=fr"><?= $textes['francais'] ?></a> | <a href="?langue=en"><?= $textes['anglais'] ?></a>
    </p>

    <h1><?= $textes['creer_profil_titre'] ?></h1>
    <p><?= $textes['creer_profil_intro'] ?></p>

    <?php if (isset($erreurs['general'])) { ?>
        <p><?= htmlspecialchars($erreurs['general']) ?></p>
    <?php } ?>

    <!-- enctype="multipart/form-data" : obligatoire pour envoyer un fichier (la photo) -->
    <form method="post" enctype="multipart/form-data">
        <!-- 1. Genre -->
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

        <!-- 2. Date de naissance -->
        <div>
            <label for="date_naissance"><?= $textes['creer_profil_date'] ?></label>
            <input type="date" id="date_naissance" name="date_naissance" required
                value="<?= htmlspecialchars($dateNaissance) ?>">
            <?php if (isset($erreurs['date_naissance'])) { ?>
                <p><?= htmlspecialchars($erreurs['date_naissance']) ?></p>
            <?php } ?>
        </div>

        <!-- 3. Photo -->
        <div>
            <label for="photo"><?= $textes['creer_profil_photo'] ?></label>
            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
            <?php if (isset($erreurs['photo'])) { ?>
                <p><?= htmlspecialchars($erreurs['photo']) ?></p>
            <?php } ?>
        </div>

        <!-- 4. Instagram -->
        <div>
            <label for="instagram"><?= $textes['creer_profil_instagram'] ?></label>
            @<input type="text" id="instagram" name="instagram" required minlength="2" maxlength="31"
                value="<?= htmlspecialchars($instagram) ?>">
            <p><?= $textes['creer_profil_instagram_aide'] ?></p>
            <?php if (isset($erreurs['instagram'])) { ?>
                <p><?= htmlspecialchars($erreurs['instagram']) ?></p>
            <?php } ?>
        </div>

        <button type="submit"><?= $textes['creer_profil_bouton'] ?></button>
    </form>

    <?php if (!$cookiesAcceptes) { ?>
        <p>
            <?= $textes['cookies_message'] ?>
            <a href="?cookies=ok"><?= $textes['cookies_ok'] ?></a>
        </p>
    <?php } ?>
</body>

</html>
