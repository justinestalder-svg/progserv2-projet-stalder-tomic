<?php

namespace Profils;

interface ProfilManagerInterface {
    public function getProfils(): array;
    public function getProfilById(int $id): ?Profil;
    public function getProfilByCompteId(int $idCompte): ?Profil;
    public function addProfil(Profil $profil): int;
    public function updateProfil(Profil $profil): bool;
    public function removeProfil(int $id): bool;
}
