<?php
// On charge la classe Database.
// __DIR__ = src/public, donc /../classes/ = src/classes/
require_once __DIR__ . '/../classes/Database.php';

// On crée l'objet : le constructeur se connecte à la base.
$database = new Database();
$pdo = $database->getPdo();

// Requête simple, sans donnée venant de l'utilisateur :
// pas besoin de bindValue ici, mais on garde prepare/execute par habitude.
$sql = "SELECT id_genre, libelle FROM genre_musical ORDER BY libelle";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// fetchAll() renvoie un tableau de lignes ([] si la table est vide).
$genres = $stmt->fetchAll();
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
                <li><?= htmlspecialchars($genre['libelle']) ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</body>

</html>
