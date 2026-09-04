<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

final class TaggedItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $name,
    ) {
    }
}
