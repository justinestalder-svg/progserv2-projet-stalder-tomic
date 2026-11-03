<?php

namespace Genres;

interface GenreMusicalManagerInterface {
    public function getGenresMusicaux(): array;
    public function getGenreMusicalById(int $id): ?GenreMusical;
    public function addGenreMusical(GenreMusical $genre): int;
    public function updateGenreMusical(GenreMusical $genre): bool;
    public function removeGenreMusical(int $id): bool;
}
