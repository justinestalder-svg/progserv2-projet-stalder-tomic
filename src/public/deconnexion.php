<?php
session_start();

// On vide la session et on la supprime
$_SESSION = [];
session_destroy();

// Retour à la page de connexion
header('Location: connexion.php');
exit();
