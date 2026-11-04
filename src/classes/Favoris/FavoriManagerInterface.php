<?php

namespace Favoris;

interface FavoriManagerInterface {

    // Récupérer tous les favoris
    public function getFavoris(): array;

    // Récupérer un favori précis
    public function getFavoriById(
        int $idProfil,
        int $idExtrait
    ): ?Favori;

    // Récupérer les favoris d'un profil
    public function getFavorisByProfil(int $idProfil): array;

    // Ajouter un favori
    public function addFavori(Favori $favori): bool;

    // Modifier un favori
    public function updateFavori(Favori $favori): bool;

    // Supprimer un favori
    public function removeFavori(
        int $idProfil,
        int $idExtrait
    ): bool;
}
