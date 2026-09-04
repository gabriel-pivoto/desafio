<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Catalog\Rarity;
use App\Domain\Catalog\RarityId;
use App\Domain\Catalog\RarityRepository;

final class PdoRarityRepository implements RarityRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function all(): array
    {
        $rows = $this->connection->select('SELECT id, slug, name FROM rarities ORDER BY sort_order, name');

        return array_map(
            static fn (array $row): Rarity => new Rarity(
                new RarityId((int) $row['id']),
                (string) $row['slug'],
                (string) $row['name'],
            ),
            $rows,
        );
    }

    public function exists(RarityId $id): bool
    {
        return $this->connection->selectOne(
            'SELECT id FROM rarities WHERE id = :id',
            ['id' => $id->value()],
        ) !== null;
    }
}
