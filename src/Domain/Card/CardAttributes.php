<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Catalog\EditionId;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\RarityId;

final class CardAttributes
{
    public function __construct(
        public readonly CardName $nameEn,
        public readonly ?CardName $namePt,
        public readonly GameId $gameId,
        public readonly EditionId $editionId,
        public readonly RarityId $rarityId,
    ) {
    }
}
