<?php

namespace Reactions;

class Reaction {
    private int $idSession;
    private int $idExtrait;
    private string $choix;
    private \DateTimeImmutable $dateReaction;

    public function __construct(
        int $idSession,
        int $idExtrait,
        string $choix,
        \DateTimeImmutable $dateReaction
    ) {
        $this->setIdSession($idSession);
        $this->setIdExtrait($idExtrait);
        $this->setChoix($choix);
        $this->setDateReaction($dateReaction);
    }

    // Getters
    public function getIdSession(): int {
        return $this->idSession;
    }

    public function getIdExtrait(): int {
        return $this->idExtrait;
    }

    public function getChoix(): string {
        return $this->choix;
    }

    public function getDateReaction(): \DateTimeImmutable {
        return $this->dateReaction;
    }

    // Setters
    public function setIdSession(int $idSession): void {
        if ($idSession <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant de la session doit être positif."
            );
        }

        $this->idSession = $idSession;
    }

    public function setIdExtrait(int $idExtrait): void {
        if ($idExtrait <= 0) {
            throw new \InvalidArgumentException(
                "L'identifiant de l'extrait doit être positif."
            );
        }

        $this->idExtrait = $idExtrait;
    }

    public function setChoix(string $choix): void {
        $choix = trim($choix);

        if ($choix === '' || mb_strlen($choix) > 10) {
            throw new \InvalidArgumentException(
                "Le choix doit contenir entre 1 et 10 caractères."
            );
        }

        $this->choix = $choix;
    }

    public function setDateReaction(
        \DateTimeImmutable $dateReaction
    ): void {
        $this->dateReaction = $dateReaction;
    }
}
