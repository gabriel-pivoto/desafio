<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Catalog\GameId;
use App\Domain\Catalog\RarityId;

final class CardCriteria
{
    public const SORTS = ['recent', 'oldest', 'name', 'name_desc'];
    public const MAX_PER_PAGE = 50;

    private function __construct(
        public readonly string $search,
        public readonly ?GameId $gameId,
        public readonly ?RarityId $rarityId,
        public readonly string $sort,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public static function create(
        string $search,
        ?int $gameId,
        ?int $rarityId,
        string $sort,
        int $page,
        int $perPage,
    ): self {
        return new self(
            trim($search),
            $gameId > 0 ? new GameId($gameId) : null,
            $rarityId > 0 ? new RarityId($rarityId) : null,
            in_array($sort, self::SORTS, true) ? $sort : self::SORTS[0],
            max($page, 1),
            min(max($perPage, 1), self::MAX_PER_PAGE),
        );
    }

    public function onPage(int $page): self
    {
        return new self($this->search, $this->gameId, $this->rarityId, $this->sort, $page, $this->perPage);
    }

    public function hasSearch(): bool
    {
        return $this->search !== '';
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
