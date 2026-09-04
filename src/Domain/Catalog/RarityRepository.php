<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

interface RarityRepository
{
    public function all(): array;

    public function exists(RarityId $id): bool;
}
