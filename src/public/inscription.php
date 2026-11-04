<?php
require_once __DIR__ . '/../utils/autoloader.php';
require_once __DIR__ . '/../utils/langue.php';

use Comptes\Compte;
use Comptes\CompteManager;

// Valeurs du formulaire (vides au premier affichage) et messages d'erreur par champ
$pseudo = '';
$email = '';
$erreurs = [];
$succes = false;

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $cguAcceptees = isset($_POST['cgu']);

    // 1. Vérification des champs
    if (strlen($pseudo) < 3 || strlen($pseudo) > 20) {
        $erreurs['pseudo'] = $textes['erreur_pseudo'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = $textes['erreur_email'];
    }

    if (
        strlen($motDePasse) < 10
        || !preg_match('/[A-Z]/', $motDePasse)
        || !preg_match('/[0-9]/', $motDePasse)
        || !preg_match('/[^A-Za-z0-9]/', $motDePasse)
    ) {
        $erreurs['mot_de_passe'] = $textes['erreur_mot_de_passe'];
    }

    if (!$cguAcceptees) {
        $erreurs['cgu'] = $textes['erreur_cgu'];
    }

    // 2. Le pseudo et l'e-mail ne doivent pas déjà être utilisés
    $compteManager = new CompteManager();

    if (!isset($erreurs['pseudo']) && $compteManager->getCompteByPseudo($pseudo) !== null) {
        $erreurs['pseudo'] = $textes['erreur_pseudo_utilise'];
    }

    if (!isset($erreurs['email']) && $compteManager->getCompteByEmail($email) !== null) {
        $erreurs['email'] = $textes['erreur_email_utilise'];
    }

    // 3. Tout est bon : on crée le compte (le mot de passe est enregistré haché, jamais en clair)
    if (empty($erreurs)) {
        $compte = new Compte(
            null,
            $pseudo,
            $email,
            password_hash($motDePasse, PASSWORD_DEFAULT),
            $langue, // la langue choisie avec le cookie
            false,
            new DateTime(),
            new DateTime()
        );

        $compteManager->addCompte($compte);

        $succes = true;
        $pseudo = '';
        $email = '';
    }
}

?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $textes['inscription_titre'] ?> | SoundMatch</title>
</head>

<body>
    <p>
        <a href="?langue=fr"><?= $textes['francais'] ?></a> | <a href="?langue=en"><?= $textes['anglais'] ?></a>
    </p>

    <h1><?= $textes['inscription_titre'] ?></h1>

    <?php if ($succes) { ?>
        <p><?= $textes['inscription_succes'] ?></p>
    <?php } ?>

    <form method="post">
        <div>
            <label for="pseudo"><?= $textes['inscription_pseudo'] ?></label>
            <input type="text" id="pseudo" name="pseudo" placeholder="ex. lea.vinyl"
                minlength="3" maxlength="20" required
                value="<?= htmlspecialchars($pseudo) ?>">
            <?php if (isset($erreurs['pseudo'])) { ?>
                <p><?= htmlspecialchars($erreurs['pseudo']) ?></p>
            <?php } ?>
        </div>

        <div>
            <label for="email"><?= $textes['inscription_email'] ?></label>
            <input type="email" id="email" name="email" placeholder="toi@mail.com" required
                value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($erreurs['email'])) { ?>
                <p><?= htmlspecialchars($erreurs['email']) ?></p>
            <?php } ?>
        </div>

        <div>
            <label for="mot_de_passe"><?= $textes['inscription_mot_de_passe'] ?></label>
            <!-- On ne remet jamais le mot de passe dans le champ après une erreur -->
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                placeholder="<?= $textes['inscription_regle_longueur'] ?>" minlength="10" required>
            <ul>
                <li><?= $textes['inscription_regle_longueur'] ?></li>
                <li><?= $textes['inscription_regle_majuscule'] ?></li>
                <li><?= $textes['inscription_regle_chiffre'] ?></li>
                <li><?= $textes['inscription_regle_special'] ?></li>
            </ul>
            <?php if (isset($erreurs['mot_de_passe'])) { ?>
                <p><?= htmlspecialchars($erreurs['mot_de_passe']) ?></p>
            <?php } ?>
        </div>

        <label for="cgu">
            <input type="checkbox" id="cgu" name="cgu" required>
            <span><?= $textes['inscription_cgu_debut'] ?> <a href="cgu.php"><?= $textes['inscription_cgu_lien'] ?></a></span>
        </label>
        <?php if (isset($erreurs['cgu'])) { ?>
            <p><?= htmlspecialchars($erreurs['cgu']) ?></p>
        <?php } ?>

        <button type="submit"><?= $textes['inscription_bouton'] ?></button>
    </form>

    <div>
        <span><?= $textes['inscription_deja_compte'] ?></span>
        <a href="connexion.php"><?= $textes['inscription_lien_connexion'] ?></a>
    </div>
</body>

</html>
