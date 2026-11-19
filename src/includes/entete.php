<?php
// En-tête commun des pages connectées (menu).
// On l'inclut dans chaque page privée avec :
// require __DIR__ . '/../includes/entete.php';
// Il utilise $textes, qui vient de utils/langue.php.
?>
<header>
    <nav>
        <a href="index.php">SoundMatch</a>
        <a href="index.php#concept"><?= htmlspecialchars($textes['menu_concept']) ?></a>
        <a href="index.php#comment"><?= htmlspecialchars($textes['menu_comment']) ?></a>
        <a href="matchs.php"><?= htmlspecialchars($textes['menu_matchs']) ?></a>
        <a href="favoris.php"><?= htmlspecialchars($textes['menu_favoris']) ?></a>
        <a href="?langue=fr">FR</a> | <a href="?langue=en">EN</a>
        <a href="reglages.php"><?= htmlspecialchars($textes['menu_reglages']) ?></a>
        <a href="deconnexion.php"><?= htmlspecialchars($textes['menu_deconnexion']) ?></a>
    </nav>
</header>
