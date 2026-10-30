<?php

namespace Comptes;

interface CompteManagerInterface {
    public function getComptes(): array;
    public function getCompteById(int $id): ?Compte;
    public function getCompteByEmail(string $email): ?Compte;
    public function addCompte(Compte $compte): int;
    public function updateCompte(Compte $compte): bool;
    public function removeCompte(int $id): bool;
}
