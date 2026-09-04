<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

interface EditionRepository
{
    public function ofGame(GameId $gameId): array;

    public function belongsToGame(EditionId $editionId, GameId $gameId): bool;
}
