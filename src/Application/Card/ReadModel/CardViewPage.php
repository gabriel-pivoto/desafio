<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

final class CardViewPage
{
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public function pages(): int
    {
        return max((int) ceil($this->total / $this->perPage), 1);
    }
}
