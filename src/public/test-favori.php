<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Favoris\Favori;

try {
    // Créer un favori fictif
    $favori = new Favori(
        1,
        1,
        new DateTimeImmutable()
    );

    // Vérifier les identifiants
    if (
        $favori->getIdProfil() !== 1 ||
        $favori->getIdExtrait() !== 1
    ) {
        throw new Exception("Erreur avec les identifiants.");
    }

    echo "<p>✅ Favori créé correctement.</p>";

    // Vérifier la date
    if (!($favori->getDateAjout() instanceof DateTimeImmutable)) {
        throw new Exception("Erreur avec la date.");
    }

    echo "<p>✅ Date d'ajout correcte.</p>";

    // Modifier la date
    $nouvelleDate = new DateTimeImmutable('2026-01-15 12:00:00');
    $favori->setDateAjout($nouvelleDate);

    if ($favori->getDateAjout() != $nouvelleDate) {
        throw new Exception("Erreur lors de la modification.");
    }

    echo "<p>✅ Modification du favori réussie.</p>";

    echo "<p>🎉 Tous les tests de la classe Favori ont réussi !</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " .
        htmlspecialchars($e->getMessage()) .
        "</p>";
}
