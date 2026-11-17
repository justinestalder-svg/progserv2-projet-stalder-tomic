<?php

namespace Reactions;

interface ReactionManagerInterface {

    // Récupérer toutes les réactions
    public function getReactions(): array;

    // Récupérer une réaction précise
    public function getReactionById(
        int $idSession,
        int $idExtrait
    ): ?Reaction;

    // Toutes les réactions d'une session
    public function getReactionsBySession(int $idSession): array;

    // Supprimer toutes les réactions d'une session
    public function removeReactionsBySession(int $idSession): bool;
    
    // Ajouter une réaction
    public function addReaction(Reaction $reaction): bool;

    // Modifier une réaction
    public function updateReaction(Reaction $reaction): bool;

    // Supprimer une réaction
    public function removeReaction(
        int $idSession,
        int $idExtrait
    ): bool;
}
