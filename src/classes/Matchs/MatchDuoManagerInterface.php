<?php

namespace Matchs;

interface MatchDuoManagerInterface {
    public function getMatchsByProfilId(int $idProfil): array;
    public function getMatchById(int $id): ?MatchDuo;
    public function getMatchByProfils(int $idProfilA, int $idProfilB): ?MatchDuo;
    public function addMatch(MatchDuo $match): ?int;
    public function updateMatch(MatchDuo $match): bool;
    public function removeMatch(int $id): bool;
}
