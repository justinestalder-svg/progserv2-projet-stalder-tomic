<?php
// Ce fichier choisit la langue de la page, la garde dans un cookie,
// puis charge les textes traduits dans $textes.
// On l'inclut en haut de chaque page, AVANT le HTML.

// Langue par défaut
$langue = 'fr';

// 1. Si on clique sur un lien de langue, on enregistre le choix dans un cookie (30 jours)
if (isset($_GET['langue'])) {
    if ($_GET['langue'] === 'fr' || $_GET['langue'] === 'en') {
        $langue = $_GET['langue'];
        setcookie('langue', $langue, time() + 60 * 60 * 24 * 30, '/');
    }
}
// 2. Sinon, on regarde si un cookie existe déjà
else if (isset($_COOKIE['langue'])) {
    if ($_COOKIE['langue'] === 'fr' || $_COOKIE['langue'] === 'en') {
        $langue = $_COOKIE['langue'];
    }
}

// 3. On charge le fichier de traduction : i18n/fr.php ou i18n/en.php
$textes = require __DIR__ . '/../i18n/' . $langue . '.php';
