<?php

namespace Sessions;

interface SessionEcouteManagerInterface {

    // Récupérer toutes les sessions
    public function getSessionsEcoute(): array;

    // Récupérer une session par son identifiant
    public function getSessionEcouteById(int $id): ?SessionEcoute;

    // Ajouter une session
    public function addSessionEcoute(SessionEcoute $session): int;

    // Modifier une session
    public function updateSessionEcoute(SessionEcoute $session): bool;

    // Supprimer une session
    public function removeSessionEcoute(int $id): bool;
}
