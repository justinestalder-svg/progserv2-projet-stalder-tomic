<?php

namespace Extraits;

interface ExtraitManagerInterface {
    public function getExtraits(): array;
    public function getExtraitById(int $id): ?Extrait;
    public function addExtrait(Extrait $extrait): int;
    public function updateExtrait(Extrait $extrait): bool;
    public function removeExtrait(int $id): bool;
}
