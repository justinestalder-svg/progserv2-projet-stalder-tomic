<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\CompteManager;

// Après 5 erreurs de mot de passe, le compte est bloqué pendant 15 minutes
const ECHECS_MAXIMUM = 5;
const DUREE_BLOCAGE = '+15 minutes';

$identifiant = '';
$erreur = null;
$compteConnecte = null;

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($identifiant === '' || $motDePasse === '') {
        $erreur = "Remplis ton adresse mail (ou ton pseudo) et ton mot de passe.";
    } else {
        $compteManager = new CompteManager();

        // On cherche le compte par adresse mail, ou par pseudo
        $compte = filter_var($identifiant, FILTER_VALIDATE_EMAIL)
            ? $compteManager->getCompteByEmail($identifiant)
            : $compteManager->getCompteByPseudo($identifiant);

        if ($compte !== null && $compte->getBloqueJusqua() !== null && $compte->getBloqueJusqua() > new DateTime()) {
            // Le compte est bloqué : on refuse, même si le mot de passe est juste
            $erreur = "Trop de tentatives. Réessaie après " . $compte->getBloqueJusqua()->format('H:i') . ".";
        } else if ($compte !== null && password_verify($motDePasse, $compte->getMotDePasseHash())) {
            // Mot de passe correct : on remet le compteur d'échecs à zéro
            $compte->setNbEchecsConnexion(0);
            $compte->setBloqueJusqua(null);
            $compteManager->updateCompte($compte);

            $compteConnecte = $compte;
            $identifiant = '';
        } else {
            // Mauvais mot de passe : on compte l'échec, et on bloque après 5 échecs
            if ($compte !== null) {
                $echecs = $compte->getNbEchecsConnexion() + 1;

                if ($echecs >= ECHECS_MAXIMUM) {
                    $compte->setBloqueJusqua(new DateTime(DUREE_BLOCAGE));
                    $echecs = 0;
                }

                $compte->setNbEchecsConnexion($echecs);
                $compteManager->updateCompte($compte);
            }

            // Toujours le même message, pour ne pas révéler si le compte existe
            $erreur = "Adresse mail, pseudo ou mot de passe incorrect.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter | SoundMatch</title>
</head>

<body>
    <h1>Se connecter</h1>

    <?php if ($compteConnecte !== null) { ?>
        <!-- Plus tard (séance sur les sessions), on redirigera vers l'accueil de la personne connectée -->
        <p>
            Connexion réussie ! Bonjour <?= htmlspecialchars($compteConnecte->getPseudo()) ?> 👋
        </p>
    <?php } ?>

    <?php if ($erreur !== null) { ?>
        <p><?= htmlspecialchars($erreur) ?></p>
    <?php } ?>

    <form method="post">
        <div>
            <label for="identifiant">Adresse mail ou pseudo</label>
            <input type="text" id="identifiant" name="identifiant" placeholder="toi@mail.com"
                required maxlength="255" autocomplete="username"
                value="<?= htmlspecialchars($identifiant) ?>">
        </div>

        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <!-- On ne remet jamais le mot de passe dans le champ après une erreur -->
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                required autocomplete="current-password">
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <div>
        <span>Pas encore de compte ?</span>
        <a href="inscription.php">Créer un compte</a>
    </div>
</body>

</html>
