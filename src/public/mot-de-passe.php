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

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}

$compteManager = new CompteManager();
$compte = $compteManager->getCompteById($_SESSION['id_compte']);

$erreurs = [];
$succes = false;

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On ne fait jamais de trim() sur un mot de passe : les espaces comptent
    $motDePasseActuel = $_POST['mot_de_passe_actuel'] ?? '';
    $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // 1. Le mot de passe actuel doit être le bon
    if (!password_verify($motDePasseActuel, $compte->getMotDePasseHash())) {
        $erreurs['actuel'] = $textes['erreur_mdp_actuel'];
    }

    // 2. Le nouveau mot de passe : les 4 règles de l'inscription...
    if (
        strlen($nouveauMotDePasse) < 10
        || !preg_match('/[A-Z]/', $nouveauMotDePasse)
        || !preg_match('/[0-9]/', $nouveauMotDePasse)
        || !preg_match('/[^A-Za-z0-9]/', $nouveauMotDePasse)
    ) {
        $erreurs['nouveau'] = $textes['erreur_mot_de_passe'];
    } else if ($nouveauMotDePasse === $motDePasseActuel) {
        // ... et différent du mot de passe actuel
        $erreurs['nouveau'] = $textes['erreur_mdp_identique'];
    }

    // 3. Les deux nouveaux mots de passe doivent être identiques
    if ($nouveauMotDePasse !== $confirmation) {
        $erreurs['confirmation'] = $textes['erreur_mdp_confirmation'];
    }

    // 4. Tout est bon : on enregistre le nouveau mot de passe (haché)
    if (empty($erreurs)) {
        $compte->setMotDePasseHash(password_hash($nouveauMotDePasse, PASSWORD_DEFAULT));
        $compteManager->updateCompte($compte);

        // Nouvel identifiant de session, par sécurité
        session_regenerate_id(true);

        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langue) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($textes['mdp_titre']) ?> | SoundMatch</title>
</head>

<body>
    <?php require __DIR__ . '/../includes/entete.php'; ?>

    <main>
        <a href="reglages.php"><?= htmlspecialchars($textes['mdp_retour']) ?></a>
        <h1><?= htmlspecialchars($textes['mdp_titre']) ?></h1>

        <?php if ($succes) { ?>
            <!-- Écran de succès -->
            <p><?= htmlspecialchars($textes['mdp_succes']) ?></p>
            <a href="reglages.php"><?= htmlspecialchars($textes['mdp_retour']) ?></a>
        <?php } else { ?>
            <form method="post">
                <!-- 1. Mot de passe actuel -->
                <div>
                    <label for="mot_de_passe_actuel"><?= htmlspecialchars($textes['mdp_actuel']) ?></label>
                    <input type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel"
                        required autocomplete="current-password">
                    <?php if (isset($erreurs['actuel'])) { ?>
                        <p><?= htmlspecialchars($erreurs['actuel']) ?></p>
                    <?php } ?>
                    <a href="mot-de-passe-oublie.php"><?= htmlspecialchars($textes['mdp_oublie_lien']) ?></a>
                </div>

                <!-- 2. Nouveau mot de passe -->
                <div>
                    <label for="nouveau_mot_de_passe"><?= htmlspecialchars($textes['mdp_nouveau']) ?></label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe"
                        required minlength="10" autocomplete="new-password">
                    <ul>
                        <li><?= htmlspecialchars($textes['inscription_regle_longueur']) ?></li>
                        <li><?= htmlspecialchars($textes['inscription_regle_majuscule']) ?></li>
                        <li><?= htmlspecialchars($textes['inscription_regle_chiffre']) ?></li>
                        <li><?= htmlspecialchars($textes['inscription_regle_special']) ?></li>
                        <li><?= htmlspecialchars($textes['mdp_regle_different']) ?></li>
                    </ul>
                    <?php if (isset($erreurs['nouveau'])) { ?>
                        <p><?= htmlspecialchars($erreurs['nouveau']) ?></p>
                    <?php } ?>
                </div>

                <!-- 3. Confirmation -->
                <div>
                    <label for="confirmation"><?= htmlspecialchars($textes['mdp_confirmation']) ?></label>
                    <input type="password" id="confirmation" name="confirmation"
                        required minlength="10" autocomplete="new-password">
                    <?php if (isset($erreurs['confirmation'])) { ?>
                        <p><?= htmlspecialchars($erreurs['confirmation']) ?></p>
                    <?php } ?>
                </div>

                <a href="reglages.php"><?= htmlspecialchars($textes['mdp_annuler']) ?></a>
                <button type="submit"><?= htmlspecialchars($textes['mdp_bouton']) ?></button>
            </form>
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
