<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Catalog\Edition;
use App\Domain\Catalog\EditionId;
use App\Domain\Catalog\EditionRepository;
use App\Domain\Catalog\GameId;

final class PdoEditionRepository implements EditionRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function ofGame(GameId $gameId): array
    {
        $rows = $this->connection->select(
            'SELECT id, game_id, code, name FROM editions WHERE game_id = :game_id ORDER BY sort_order, name',
            ['game_id' => $gameId->value()],
        );

        return array_map(
            static fn (array $row): Edition => new Edition(
                new EditionId((int) $row['id']),
                new GameId((int) $row['game_id']),
                (string) $row['code'],
                (string) $row['name'],
            ),
            $rows,
        );
    }

    public function belongsToGame(EditionId $editionId, GameId $gameId): bool
    {
        return $this->connection->selectOne(
            'SELECT id FROM editions WHERE id = :id AND game_id = :game_id',
            ['id' => $editionId->value(), 'game_id' => $gameId->value()],
        ) !== null;
    }
}
