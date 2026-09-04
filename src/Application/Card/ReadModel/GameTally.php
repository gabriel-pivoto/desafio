<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

final class GameTally
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly int $total,
    ) {
    }
}
