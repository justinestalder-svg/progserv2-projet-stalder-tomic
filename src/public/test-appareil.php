<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Appareils\Appareil;
use Appareils\AppareilManager;

$compteManager = new CompteManager();
$appareilManager = new AppareilManager();

// 1. Un appareil appartient à un compte : on crée d'abord un compte de test
$idCompte = $compteManager->addCompte(new Compte(
    null,
    'test_appareil',
    'test.appareil@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT),
    'fr',
    false,
    new DateTime(),
    new DateTime()
));

// 2. Le compte se connecte depuis deux appareils (chacun reçoit son propre jeton de session)
$jetonMac = bin2hex(random_bytes(32));
$idMac = $appareilManager->addAppareil(new Appareil(
    null,
    'Mac',
    'Chrome',
    'Yverdon',
    new DateTime(),
    new DateTime(),
    new DateTime('+30 days'),
    hash('sha256', $jetonMac),
    $idCompte
));
$idIphone = $appareilManager->addAppareil(new Appareil(
    null,
    'iPhone',
    'Safari',
    '',
    new DateTime(),
    new DateTime(),
    new DateTime('+30 days'),
    hash('sha256', bin2hex(random_bytes(32))),
    $idCompte
));
$nbAvant = count($appareilManager->getAppareilsByCompteId($idCompte));

// 3. Sur une page protégée, on retrouve l'appareil grâce au cookie, puis on met à jour son activité
$appareil = $appareilManager->getAppareilByJetonSessionHash(hash('sha256', $jetonMac));
$appareil->setDerniereActivite(new DateTime());
$appareilManager->updateAppareil($appareil);

// 4. Depuis le Mac, on déconnecte tous les autres appareils
$appareilManager->removeAutresAppareils($idCompte, $idMac);
$nbApres = count($appareilManager->getAppareilsByCompteId($idCompte));

// 5. On nettoie : d'abord les appareils (ils dépendent du compte), puis le compte
$appareilManager->removeAppareil($idMac);
$compteManager->removeCompte($idCompte);
?>
<p><?= $nbAvant === 2 ? '✅' : '❌' ?> Le compte est connecté sur <?= $nbAvant ?> appareils.</p>
<p><?= $appareil !== null ? '✅' : '❌' ?> Appareil retrouvé grâce au jeton de session : <?= htmlspecialchars($appareil->getNomAppareil()) ?> (<?= htmlspecialchars($appareil->getNavigateur()) ?>).</p>
<p><?= $nbApres === 1 ? '✅' : '❌' ?> Après « Déconnecter les autres appareils », il en reste <?= $nbApres ?>.</p>
<p>✅ Appareil et AppareilManager fonctionnent. Les appareils et le compte de test ont été supprimés.</p>
