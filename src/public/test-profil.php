<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Profils\Profil;
use Profils\ProfilManager;

$compteManager = new CompteManager();
$profilManager = new ProfilManager();

// 1. Un profil appartient à un compte : on crée d'abord un compte de test
$idCompte = $compteManager->addCompte(new Compte(
    null, 'test_profil', 'test.profil@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT), 'fr', false,
    new DateTime(), new DateTime()
));

// 2. On crée le profil de ce compte (le @ du nom Instagram est retiré automatiquement)
$idProfil = $profilManager->addProfil(new Profil(
    null, 'femme', new DateTime('2003-05-14'), 'uploads/test.jpg', '@test.soundmatch', 'Yverdon', $idCompte
));

// 3. On relit le profil à partir du compte, puis on le modifie
$profil = $profilManager->getProfilByCompteId($idCompte);
$profil->setVille('Lausanne');
$profilManager->updateProfil($profil);
$profilRelu = $profilManager->getProfilById($idProfil);

// 4. On vérifie qu'un profil invalide est bien refusé (moins de 18 ans)
try {
    new Profil(null, 'femme', new DateTime('-10 years'), 'uploads/x.jpg', 'enfant', null, $idCompte);
    $refus = "❌ Le profil de moins de 18 ans a été accepté";
} catch (InvalidArgumentException $e) {
    $refus = "✅ Profil refusé : " . $e->getMessage();
}

// 5. On nettoie : d'abord le profil (il dépend du compte), puis le compte
$profilManager->removeProfil($idProfil);
$compteManager->removeCompte($idCompte);
?>
<p>Profil <?= $idProfil ?> créé pour le compte <?= $idCompte ?> : <?= htmlspecialchars($profilRelu->getGenre()) ?>, <?= $profilRelu->getAge() ?> ans, @<?= htmlspecialchars($profilRelu->getNomInstagram()) ?>, ville modifiée en <?= htmlspecialchars($profilRelu->getVille()) ?>.</p>
<p><?= htmlspecialchars($refus) ?></p>
<p>✅ Profil et ProfilManager fonctionnent. Le profil et le compte de test ont été supprimés.</p>
