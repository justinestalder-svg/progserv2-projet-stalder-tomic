<?php

namespace Jetons;

interface JetonVerificationManagerInterface {
    public function getJetons(): array;
    public function getJetonById(int $id): ?JetonVerification;
    public function getJetonByValeurHash(string $valeurHash): ?JetonVerification;
    public function addJeton(JetonVerification $jeton): int;
    public function updateJeton(JetonVerification $jeton): bool;
    public function removeJeton(int $id): bool;
}
