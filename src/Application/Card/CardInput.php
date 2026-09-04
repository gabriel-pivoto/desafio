<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Port\UploadedImage;

final class CardInput
{
    public function __construct(
        public readonly string $nameEn,
        public readonly string $namePt,
        public readonly ?int $gameId,
        public readonly ?int $editionId,
        public readonly ?int $rarityId,
        public readonly ?UploadedImage $image,
        public readonly bool $removeImage,
    ) {
    }
}
