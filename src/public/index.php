<?php
// On charge l'autoloader : il charge automatiquement toutes les classes dont on a besoin.
// __DIR__ = src/public, donc /../utils/ = src/utils/
require_once __DIR__ . '/../utils/autoloader.php';

use Genres\GenreMusicalManager;

// La page ne fait plus de SQL elle-même : elle demande les genres au manager.
// getGenresMusicaux() renvoie un tableau d'objets GenreMusical, triés par libellé.
$genreManager = new GenreMusicalManager();
$genres = $genreManager->getGenresMusicaux();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundMatch</title>
</head>

<body>
    <h1>SoundMatch</h1>
    <p>Projet réalisé dans le cadre du cours ProgServ2 à la HEIG-VD.</p>

    <h2>Genres musicaux</h2>

    <?php if (empty($genres)) { ?>
        <p>Aucun genre dans la base de données.</p>
    <?php } else { ?>
        <ul>
            <?php foreach ($genres as $genre) { ?>
                <!-- htmlspecialchars : protection XSS, même pour des données de la base -->
                <li><?= htmlspecialchars($genre->getLibelle()) ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</body>

</html>
