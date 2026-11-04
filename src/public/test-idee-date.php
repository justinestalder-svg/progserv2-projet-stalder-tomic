<?php

require_once __DIR__ . '/../utils/autoloader.php';

use Idees\IdeeDate;

try {
    // Créer une idée de date fictive
    $idee = new IdeeDate(
        1,
        1,
        "Concert de pop",
        "Aller à un concert ensemble."
    );

    // Vérifier la création
    if (
        $idee->getIdIdee() !== 1 ||
        $idee->getIdGenre() !== 1 ||
        $idee->getTitre() !== "Concert de pop"
    ) {
        throw new Exception("Erreur lors de la création.");
    }

    echo "<p>✅ Idée de date créée correctement.</p>";

    // Modifier le titre
    $idee->setTitre("Festival de musique");

    if ($idee->getTitre() !== "Festival de musique") {
        throw new Exception("Erreur lors de la modification.");
    }

    echo "<p>✅ Titre modifié correctement.</p>";

    // Modifier la description
    $idee->setDescription("Découvrir un festival ensemble.");

    if ($idee->getDescription() !== "Découvrir un festival ensemble.") {
        throw new Exception("Erreur avec la description.");
    }

    echo "<p>✅ Description modifiée correctement.</p>";

    echo "<p>🎉 Tous les tests de la classe IdeeDate ont réussi !</p>";
} catch (Throwable $e) {
    echo "<p>❌ Erreur : " .
        htmlspecialchars($e->getMessage()) .
        "</p>";
}
