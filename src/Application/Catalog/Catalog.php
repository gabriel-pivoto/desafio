<?php

declare(strict_types=1);

namespace App\Application\Catalog;

final class Catalog
{
    public function __construct(
        public readonly array $games,
        public readonly array $rarities,
    ) {
    }
}
