<?php

namespace Appareils;

interface AppareilManagerInterface {
    public function getAppareilsByCompteId(int $idCompte): array;
    public function getAppareilById(int $id): ?Appareil;
    public function getAppareilByJetonSessionHash(string $jetonSessionHash): ?Appareil;
    public function addAppareil(Appareil $appareil): int;
    public function updateAppareil(Appareil $appareil): bool;
    public function removeAppareil(int $id): bool;
    public function removeAutresAppareils(int $idCompte, int $idAppareilActuel): bool;
}
