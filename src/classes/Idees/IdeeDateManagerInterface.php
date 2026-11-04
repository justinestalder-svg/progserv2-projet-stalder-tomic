<?php

namespace Idees;

interface IdeeDateManagerInterface {
    public function getIdeesDate(): array;

    public function getIdeeDateById(int $idIdee): ?IdeeDate;

    public function getIdeesDateByGenre(int $idGenre): array;

    public function addIdeeDate(IdeeDate $ideeDate): bool;

    public function updateIdeeDate(IdeeDate $ideeDate): bool;

    public function removeIdeeDate(int $idIdee): bool;
}
