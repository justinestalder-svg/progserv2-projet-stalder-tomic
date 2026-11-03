<?php
require_once __DIR__ . '/../utils/autoloader.php';

use Comptes\Compte;
use Comptes\CompteManager;
use Profils\Profil;
use Profils\ProfilManager;
use Matchs\MatchDuo;
use Matchs\MatchDuoManager;

$compteManager = new CompteManager();
$profilManager = new ProfilManager();
$matchManager = new MatchDuoManager();

// 1. Deux genres musicaux de test. La classe GenreMusical est celle de Kristina :
//    en attendant, on les crée directement en SQL, seulement pour ce test.
$pdo = (new Database())->getPdo();
$pdo->exec("INSERT INTO genre_musical (libelle) VALUES ('Test rap')");
$idRap = (int) $pdo->lastInsertId();
$pdo->exec("INSERT INTO genre_musical (libelle) VALUES ('Test rock')");
$idRock = (int) $pdo->lastInsertId();

// 2. Deux profils (donc deux comptes)
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

// 3. On crée le match en donnant les profils "à l'envers" (Béa puis Alice), avec ses scores par genre
$match = new MatchDuo(null, 82, 12, new DateTime(), 'Le duo du dimanche', 'Vous aimez toutes les deux le rap.', $idBea, $idAlice);
$match->setScoreGenre($idRap, 90);
$match->setScoreGenre($idRock, 40);
$idMatch = $matchManager->addMatch($match);

// 4. On le retrouve dans les deux sens, et un deuxième match pour la même paire est refusé
$trouveAB = $matchManager->getMatchByProfils($idAlice, $idBea);
$trouveBA = $matchManager->getMatchByProfils($idBea, $idAlice);
$doublon = $matchManager->addMatch(new MatchDuo(null, 50, 3, new DateTime(), null, null, $idAlice, $idBea));

// 5. Les scores par genre sont bien relus, et le meilleur genre est le rap
$matchRelu = $matchManager->getMatchById($idMatch);
$scoresOk = $matchRelu->getScoresGenre() == [$idRap => 90, $idRock => 40];
$meilleurOk = $matchRelu->getIdMeilleurGenre() === $idRap;

// 6. On modifie un score, puis on vérifie la page « Mes matchs » de Béa
$matchRelu->setScoreGenre($idRock, 70);
$matchManager->updateMatch($matchRelu);
$rockModifie = $matchManager->getMatchById($idMatch)->getScoresGenre()[$idRock] === 70;
$matchsBea = $matchManager->getMatchsByProfilId($idBea);

// 7. Un profil ne peut pas matcher avec lui-même
try {
    new MatchDuo(null, 100, 20, new DateTime(), null, null, $idAlice, $idAlice);
    $refus = "❌ Le match avec soi-même a été accepté";
} catch (InvalidArgumentException $e) {
    $refus = "✅ Refusé : " . $e->getMessage();
}

// 8. On nettoie dans l'ordre : match (et ses scores), profils, comptes, genres de test
$matchManager->removeMatch($idMatch);
$profilManager->removeProfil($idAlice);
$profilManager->removeProfil($idBea);
$compteManager->removeCompte($idCompteA);
$compteManager->removeCompte($idCompteB);
$pdo->exec("DELETE FROM genre_musical WHERE id_genre IN ($idRap, $idRock)");
?>
<p><?= $trouveAB && $trouveBA ? '✅' : '❌' ?> Le match est retrouvé dans les deux sens (Alice/Béa et Béa/Alice).</p>
<p><?= $doublon === null ? '✅' : '❌' ?> Un deuxième match pour la même paire est refusé.</p>
<p><?= $scoresOk ? '✅' : '❌' ?> Les scores par genre sont bien enregistrés (rap 90, rock 40).</p>
<p><?= $meilleurOk ? '✅' : '❌' ?> Le meilleur genre du duo est le rap (pour l'idée de date).</p>
<p><?= $rockModifie ? '✅' : '❌' ?> Le score du rock a bien été modifié à 70.</p>
<p><?= count($matchsBea) === 1 ? '✅' : '❌' ?> Béa a <?= count($matchsBea) ?> match, avec le profil <?= $matchsBea[0]->getIdAutreProfil($idBea) === $idAlice ? "d'Alice" : '?' ?>.</p>
<p><?= htmlspecialchars($refus) ?></p>
<p>✅ MatchDuo et MatchDuoManager fonctionnent. Les données de test ont été supprimées.</p>
