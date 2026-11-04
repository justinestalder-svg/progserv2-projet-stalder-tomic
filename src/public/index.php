<?php
// On charge l'autoloader : il charge automatiquement toutes les classes dont on a besoin.
require_once __DIR__ . '/../utils/autoloader.php';

// On choisit la langue (cookie) et on charge les textes : avant le HTML
require_once __DIR__ . '/../utils/langue.php';

use Genres\GenreMusicalManager;

// La page ne fait pas de SQL elle-même : elle demande les genres au manager.
$genreManager = new GenreMusicalManager();
$genres = $genreManager->getGenresMusicaux();
?>

<!DOCTYPE html>
<html lang="<?= $langue ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundMatch</title>
</head>

<body>
    <p>
        <a href="?langue=fr"><?= $textes['francais'] ?></a> | <a href="?langue=en"><?= $textes['anglais'] ?></a>
    </p>

    <h1>SoundMatch</h1>
    <p><?= $textes['accueil_description'] ?></p>

    <h2><?= $textes['accueil_genres'] ?></h2>

    <?php if (empty($genres)) { ?>
        <p><?= $textes['accueil_aucun_genre'] ?></p>
    <?php } else { ?>
        <ul>
            <?php foreach ($genres as $genre) { ?>
                <!-- htmlspecialchars : protection XSS, même pour des données de la base -->
                <li><?= htmlspecialchars($genre->getLibelle()) ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</body>

<?php if (!$cookiesAcceptes) { ?>
    <p>
        <?= $textes['cookies_message'] ?>
        <a href="?cookies=ok"><?= $textes['cookies_ok'] ?></a>
    </p>
<?php } ?>

</html>
