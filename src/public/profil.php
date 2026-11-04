<?php
session_start();
require_once __DIR__ . '/../utils/autoloader.php';
require_once __DIR__ . '/../utils/langue.php';

// Page privée : si personne n'est connecté, on renvoie vers la connexion
if (!isset($_SESSION['id_compte'])) {
    header('Location: connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $textes['profil_titre'] ?> | SoundMatch</title>
</head>

<body>
    <p>
        <a href="?langue=fr"><?= $textes['francais'] ?></a> | <a href="?langue=en"><?= $textes['anglais'] ?></a>
    </p>

    <h1><?= $textes['profil_titre'] ?></h1>

    <p><?= $textes['profil_bonjour'] ?> <?= htmlspecialchars($_SESSION['pseudo']) ?> 👋</p>

    <a href="deconnexion.php"><?= $textes['profil_deconnexion'] ?></a>
</body>

</html>
