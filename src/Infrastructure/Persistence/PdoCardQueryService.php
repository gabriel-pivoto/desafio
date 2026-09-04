<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Card\ReadModel\CardQueryService;
use App\Application\Card\ReadModel\CardSummary;
use App\Application\Card\ReadModel\CardView;
use App\Application\Card\ReadModel\CardViewPage;
use App\Application\Card\ReadModel\GameTally;
use App\Domain\Card\CardCriteria;
use App\Domain\Card\CardId;

final class PdoCardQueryService implements CardQueryService
{
    private const SELECT = 'SELECT c.id, c.name_en, c.name_pt, c.image_path, c.created_at, c.updated_at,
                                   g.id AS game_id, g.slug AS game_slug, g.name AS game_name,
                                   e.id AS edition_id, e.code AS edition_code, e.name AS edition_name,
                                   r.id AS rarity_id, r.slug AS rarity_slug, r.name AS rarity_name
                              FROM cards c
                              JOIN games g ON g.id = c.game_id
                              JOIN editions e ON e.id = c.edition_id
                              JOIN rarities r ON r.id = c.rarity_id';

    public function __construct(
        private Connection $connection,
        private CardViewMapper $mapper,
    ) {
    }

    public function paginate(CardCriteria $criteria): CardViewPage
    {
        $query = new CardQuery($criteria);
        $parameters = $query->parameters();

        $total = (int) ($this->connection->selectOne(
            'SELECT COUNT(*) AS total FROM cards c' . $query->where(),
            $parameters,
        )['total'] ?? 0);

        $pages = max((int) ceil($total / $criteria->perPage), 1);
        $bounded = $criteria->onPage(min($criteria->page, $pages));

        $rows = $this->connection->select(
            self::SELECT . $query->where() . $query->orderBy()
                . sprintf(' LIMIT %d OFFSET %d', $bounded->perPage, $bounded->offset()),
            $parameters,
        );

        return new CardViewPage(
            array_map($this->mapper->map(...), $rows),
            $total,
            $bounded->page,
            $bounded->perPage,
        );
    }

    public function ofId(CardId $id): ?CardView
    {
        $row = $this->connection->selectOne(self::SELECT . ' WHERE c.id = :id', ['id' => $id->value()]);

        return $row === null ? null : $this->mapper->map($row);
    }

    public function summary(): CardSummary
    {
        $rows = $this->connection->select(
            'SELECT g.slug, g.name, COUNT(c.id) AS total
               FROM games g
               LEFT JOIN cards c ON c.game_id = g.id
              GROUP BY g.id, g.slug, g.name
              ORDER BY g.sort_order',
        );

        return CardSummary::fromTallies(array_map(
            static fn (array $row): GameTally => new GameTally(
                (string) $row['slug'],
                (string) $row['name'],
                (int) $row['total'],
            ),
            $rows,
        ));
    }
}
