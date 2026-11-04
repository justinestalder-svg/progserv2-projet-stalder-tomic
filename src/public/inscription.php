<?php
require_once __DIR__ . '/../utils/autoloader.php';

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
        $erreurs['pseudo'] = "Le pseudo doit contenir entre 3 et 20 caractères.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "L'adresse e-mail n'est pas valide.";
    }

    if (
        strlen($motDePasse) < 10
        || !preg_match('/[A-Z]/', $motDePasse)
        || !preg_match('/[0-9]/', $motDePasse)
        || !preg_match('/[^A-Za-z0-9]/', $motDePasse)
    ) {
        $erreurs['mot_de_passe'] = "Le mot de passe ne respecte pas toutes les règles.";
    }

    if (!$cguAcceptees) {
        $erreurs['cgu'] = "Tu dois accepter les termes et conditions.";
    }

    // 2. Le pseudo et l'e-mail ne doivent pas déjà être utilisés
    $compteManager = new CompteManager();

    if (!isset($erreurs['pseudo']) && $compteManager->getCompteByPseudo($pseudo) !== null) {
        $erreurs['pseudo'] = "Ce pseudo est déjà utilisé.";
    }

    if (!isset($erreurs['email']) && $compteManager->getCompteByEmail($email) !== null) {
        $erreurs['email'] = "Cette adresse e-mail est déjà utilisée.";
    }

    // 3. Tout est bon : on crée le compte (le mot de passe est enregistré haché, jamais en clair)
    if (empty($erreurs)) {
        $compte = new Compte(
            null,
            $pseudo,
            $email,
            password_hash($motDePasse, PASSWORD_DEFAULT),
            'fr',
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
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer ton compte | SoundMatch</title>
</head>

<body>
    <h1>Créer ton compte</h1>

    <?php if ($succes) { ?>
        <p>Ton compte a été créé ! Tu peux maintenant te connecter.</p>
    <?php } ?>

    <form method="post">
        <div>
            <label for="pseudo">Nom d'utilisateur / pseudo</label>
            <input type="text" id="pseudo" name="pseudo" placeholder="ex. lea.vinyl"
                minlength="3" maxlength="20" required
                value="<?= htmlspecialchars($pseudo) ?>">
            <?php if (isset($erreurs['pseudo'])) { ?>
                <p><?= htmlspecialchars($erreurs['pseudo']) ?></p>
            <?php } ?>
        </div>

        <div>
            <label for="email">Adresse mail</label>
            <input type="email" id="email" name="email" placeholder="toi@mail.com" required
                value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($erreurs['email'])) { ?>
                <p><?= htmlspecialchars($erreurs['email']) ?></p>
            <?php } ?>
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <!-- On ne remet jamais le mot de passe dans le champ après une erreur -->
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                placeholder="Minimum 10 caractères" minlength="10" required>
            <ul>
                <li>10 caractères minimum</li>
                <li>Une majuscule</li>
                <li>Un chiffre</li>
                <li>Un caractère spécial</li>
            </ul>
            <?php if (isset($erreurs['mot_de_passe'])) { ?>
                <p><?= htmlspecialchars($erreurs['mot_de_passe']) ?></p>
            <?php } ?>
        </div>

        <label for="cgu">
            <input type="checkbox" id="cgu" name="cgu" required>
            <span>J'accepte les <a href="cgu.php">termes et conditions</a></span>
        </label>
        <?php if (isset($erreurs['cgu'])) { ?>
            <p><?= htmlspecialchars($erreurs['cgu']) ?></p>
        <?php } ?>

        <button type="submit">Créer mon compte</button>
    </form>

    <div>
        <span>Déjà un compte ?</span>
        <a href="connexion.php">Se connecter</a>
    </div>
</body>

</html>
