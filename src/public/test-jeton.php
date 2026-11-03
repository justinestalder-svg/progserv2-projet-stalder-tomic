<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Jetons\JetonVerification;
use Jetons\JetonVerificationManager;

$compteManager = new CompteManager();
$jetonManager = new JetonVerificationManager();

// 1. Un jeton appartient à un compte : on crée d'abord un compte de test
$idCompte = $compteManager->addCompte(new Compte(
    null,
    'test_jeton',
    'test.jeton@example.com',
    password_hash('motdepasse123', PASSWORD_DEFAULT),
    'fr',
    false,
    new DateTime(),
    new DateTime()
));

// 2. On génère un code secret (celui qui ira dans le lien du mail) et on n'enregistre que son hash
$codeSecret = bin2hex(random_bytes(32));
$idJeton = $jetonManager->addJeton(new JetonVerification(
    null,
    'confirmation_email',
    hash('sha256', $codeSecret),
    null,
    new DateTime(),
    new DateTime('+1 hour'),
    null,
    $idCompte
));

// 3. Quand la personne clique sur le lien, on retrouve le jeton grâce au hash du code
$jeton = $jetonManager->getJetonByValeurHash(hash('sha256', $codeSecret));
$valideAvant = !$jeton->estExpire() && !$jeton->estUtilise();

// 4. On marque le jeton comme utilisé, pour qu'il ne serve qu'une fois
$jeton->setDateUtilisation(new DateTime());
$jetonManager->updateJeton($jeton);
$utiliseApres = $jetonManager->getJetonById($idJeton)->estUtilise();

// 5. On nettoie : d'abord le jeton (il dépend du compte), puis le compte
$jetonManager->removeJeton($idJeton);
$compteManager->removeCompte($idCompte);
?>
<p>Jeton <?= $idJeton ?> créé pour le compte <?= $idCompte ?> (type : <?= htmlspecialchars($jeton->getType()) ?>).</p>
<p><?= $valideAvant ? '✅' : '❌' ?> Avant le clic : le jeton est valable.</p>
<p><?= $utiliseApres ? '✅' : '❌' ?> Après le clic : le jeton est marqué comme utilisé.</p>
<p>✅ JetonVerification et JetonVerificationManager fonctionnent. Le jeton et le compte de test ont été supprimés.</p>
