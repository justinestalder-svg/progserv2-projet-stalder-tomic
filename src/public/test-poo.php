<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;

$compteManager = new CompteManager();

// Crée un compte de test
$compte = new Compte(
    null,
    'test_poo',
    'test@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT),
    'fr',
    false,
    new DateTime(),
    new DateTime()
);
$id = $compteManager->addCompte($compte);

// Relit le compte depuis la base
$compteRelu = $compteManager->getCompteById($id);

// Supprime le compte de test
$compteManager->removeCompte($id);
?>
<p>Compte créé avec l'id <?= $id ?>, relu : <?= htmlspecialchars($compteRelu->getPseudo()) ?> (<?= htmlspecialchars($compteRelu->getEmail()) ?>), puis supprimé.</p>
<p>✅ Database, autoloader, Compte et CompteManager fonctionnent.</p>
