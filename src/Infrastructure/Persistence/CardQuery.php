<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Card\CardCriteria;

final class CardQuery
{
    private const ORDER_BY = [
        'recent' => 'c.created_at DESC, c.id DESC',
        'oldest' => 'c.created_at ASC, c.id ASC',
        'name' => 'c.name_en ASC',
        'name_desc' => 'c.name_en DESC',
    ];

    public function __construct(private CardCriteria $criteria)
    {
    }

    public function where(): string
    {
        $clauses = [];

        if ($this->criteria->hasSearch()) {
            $clauses[] = '(c.name_en LIKE :search_en OR c.name_pt LIKE :search_pt)';
        }

        if ($this->criteria->gameId !== null) {
            $clauses[] = 'c.game_id = :game_id';
        }

        if ($this->criteria->rarityId !== null) {
            $clauses[] = 'c.rarity_id = :rarity_id';
        }

        return $clauses === [] ? '' : ' WHERE ' . implode(' AND ', $clauses);
    }

    public function parameters(): array
    {
        $parameters = [];

        if ($this->criteria->hasSearch()) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $this->criteria->search) . '%';
            $parameters['search_en'] = $term;
            $parameters['search_pt'] = $term;
        }

        if ($this->criteria->gameId !== null) {
            $parameters['game_id'] = $this->criteria->gameId->value();
        }

        if ($this->criteria->rarityId !== null) {
            $parameters['rarity_id'] = $this->criteria->rarityId->value();
        }

        return $parameters;
    }

    public function orderBy(): string
    {
        return ' ORDER BY ' . (self::ORDER_BY[$this->criteria->sort] ?? self::ORDER_BY['recent']);
    }
}
