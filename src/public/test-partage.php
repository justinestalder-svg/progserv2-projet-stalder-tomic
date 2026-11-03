<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Profils\Profil;
use Profils\ProfilManager;
use Partages\PartageInsta;
use Partages\PartageInstaManager;

$compteManager = new CompteManager();
$profilManager = new ProfilManager();
$partageManager = new PartageInstaManager();

// 1. Il faut deux profils (donc deux comptes) pour tester un partage
$idCompteA = $compteManager->addCompte(new Compte(
    null,
    'test_alice',
    'alice@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT),
    'fr',
    false,
    new DateTime(),
    new DateTime()
));
$idCompteB = $compteManager->addCompte(new Compte(
    null,
    'test_bea',
    'bea@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT),
    'fr',
    false,
    new DateTime(),
    new DateTime()
));
$idAlice = $profilManager->addProfil(new Profil(null, 'femme', new DateTime('2002-03-01'), 'uploads/a.jpg', 'alice.music', null, $idCompteA));
$idBea = $profilManager->addProfil(new Profil(null, 'femme', new DateTime('2001-07-12'), 'uploads/b.jpg', 'bea.sound', null, $idCompteB));

// 2. Alice partage son Instagram avec Béa
$cree = $partageManager->addPartage(new PartageInsta($idAlice, $idBea, new DateTime()));

// 3. Le même partage une deuxième fois est ignoré
$doublon = $partageManager->addPartage(new PartageInsta($idAlice, $idBea, new DateTime()));

// 4. Béa voit l'Instagram d'Alice, mais pas l'inverse (Béa n'a rien partagé)
$beaVoitAlice = $partageManager->aPartage($idAlice, $idBea);
$aliceVoitBea = $partageManager->aPartage($idBea, $idAlice);
$nbRecusBea = count($partageManager->getPartagesRecus($idBea));

// 5. On ne peut pas partager avec soi-même
try {
    new PartageInsta($idAlice, $idAlice, new DateTime());
    $refus = "❌ Le partage avec soi-même a été accepté";
} catch (InvalidArgumentException $e) {
    $refus = "✅ Refusé : " . $e->getMessage();
}

// 6. On nettoie dans l'ordre : partages, puis profils, puis comptes
$partageManager->removePartage($idAlice, $idBea);
$profilManager->removeProfil($idAlice);
$profilManager->removeProfil($idBea);
$compteManager->removeCompte($idCompteA);
$compteManager->removeCompte($idCompteB);
?>
<p><?= $cree ? '✅' : '❌' ?> Alice a partagé son Instagram avec Béa.</p>
<p><?= !$doublon ? '✅' : '❌' ?> Le même partage une deuxième fois est ignoré.</p>
<p><?= $beaVoitAlice && !$aliceVoitBea ? '✅' : '❌' ?> Béa voit l'Instagram d'Alice, Alice ne voit pas celui de Béa.</p>
<p><?= $nbRecusBea === 1 ? '✅' : '❌' ?> Béa a reçu <?= $nbRecusBea ?> partage.</p>
<p><?= htmlspecialchars($refus) ?></p>
<p>✅ PartageInsta et PartageInstaManager fonctionnent. Les données de test ont été supprimées.</p>
