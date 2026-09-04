<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\EditionRepository;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\GameRepository;
use App\Domain\Shared\Exception\EntityNotFound;

final class ListEditionsOfGame
{
    public function __construct(
        private GameRepository $games,
        private EditionRepository $editions,
    ) {
    }

    public function __invoke(string $gameSlugOrId): array
    {
        $game = is_numeric($gameSlugOrId)
            ? $this->games->ofId(new GameId((int) $gameSlugOrId))
            : $this->games->ofSlug($gameSlugOrId);

        if ($game === null) {
            throw EntityNotFound::game();
        }

        return $this->editions->ofGame($game->id());
    }
}
