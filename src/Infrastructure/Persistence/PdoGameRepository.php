<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Catalog\Game;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\GameRepository;

final class PdoGameRepository implements GameRepository
{
    private const SELECT = 'SELECT id, slug, name FROM games';

    public function __construct(private Connection $connection)
    {
    }

    public function all(): array
    {
        $rows = $this->connection->select(self::SELECT . ' ORDER BY sort_order, name');

        return array_map($this->toGame(...), $rows);
    }

    public function ofId(GameId $id): ?Game
    {
        $row = $this->connection->selectOne(self::SELECT . ' WHERE id = :id', ['id' => $id->value()]);

        return $row === null ? null : $this->toGame($row);
    }

    public function ofSlug(string $slug): ?Game
    {
        $row = $this->connection->selectOne(self::SELECT . ' WHERE slug = :slug', ['slug' => $slug]);

        return $row === null ? null : $this->toGame($row);
    }

    private function toGame(array $row): Game
    {
        return new Game(new GameId((int) $row['id']), (string) $row['slug'], (string) $row['name']);
    }
}
