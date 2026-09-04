<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

interface GameRepository
{
    public function all(): array;

    public function ofId(GameId $id): ?Game;

    public function ofSlug(string $slug): ?Game;
}
