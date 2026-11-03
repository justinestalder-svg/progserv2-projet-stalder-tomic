<?php

namespace Series;

interface SerieManagerInterface {

    // Gestion des séries
    public function getSeries(): array;
    public function getSerieById(int $id): ?Serie;
    public function addSerie(Serie $serie): int;
    public function updateSerie(Serie $serie): bool;
    public function removeSerie(int $id): bool;

    // Gestion des extraits dans une série
    public function addExtraitToSerie(
        int $idSerie,
        int $idExtrait,
        int $rang
    ): bool;

    public function getExtraitsBySerie(int $idSerie): array;

    public function removeExtraitFromSerie(
        int $idSerie,
        int $idExtrait
    ): bool;
}
