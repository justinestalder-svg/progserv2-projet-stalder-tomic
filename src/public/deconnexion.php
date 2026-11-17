<?php
// Cookie de session protégé : le JavaScript ne peut pas le lire (httponly)
// et il n'est pas envoyé par les autres sites (samesite)
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// On vide la session et on la supprime
$_SESSION = [];
session_destroy();

// Retour à la page de connexion
header('Location: connexion.php');
exit();
