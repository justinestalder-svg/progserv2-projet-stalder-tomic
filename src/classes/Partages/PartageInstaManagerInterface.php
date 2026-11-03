<?php

namespace Partages;

interface PartageInstaManagerInterface {
    public function getPartagesRecus(int $idProfil): array;
    public function getPartagesEnvoyes(int $idProfil): array;
    public function aPartage(int $idProfilPartageur, int $idProfilDestinataire): bool;
    public function addPartage(PartageInsta $partage): bool;
    public function removePartage(int $idProfilPartageur, int $idProfilDestinataire): bool;
}
