<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

final class CardView
{
    public function __construct(
        public readonly int $id,
        public readonly string $nameEn,
        public readonly ?string $namePt,
        public readonly ?string $imagePath,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly TaggedItem $game,
        public readonly TaggedItem $edition,
        public readonly TaggedItem $rarity,
    ) {
    }
}
