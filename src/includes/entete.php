<?php
// En-tête commun des pages connectées (menu).
// On l'inclut dans chaque page privée avec :
// require __DIR__ . '/../includes/entete.php';
// Il utilise $textes, qui vient de utils/langue.php.
?>
<header>
    <nav>
        <a href="index.php">SoundMatch</a>
        <a href="index.php#concept"><?= $textes['menu_concept'] ?></a>
        <a href="index.php#comment"><?= $textes['menu_comment'] ?></a>
        <a href="matchs.php"><?= $textes['menu_matchs'] ?></a>
        <a href="favoris.php"><?= $textes['menu_favoris'] ?></a>
        <a href="?langue=fr">FR</a> | <a href="?langue=en">EN</a>
        <a href="reglages.php"><?= $textes['menu_reglages'] ?></a>
        <a href="deconnexion.php"><?= $textes['menu_deconnexion'] ?></a>
    </nav>
</header>
