<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Catalog\EditionId;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\RarityId;

final class Card
{
    private function __construct(
        private ?CardId $id,
        private CardAttributes $attributes,
        private ?CardImage $image,
    ) {
    }

    public static function register(CardAttributes $attributes, ?CardImage $image): self
    {
        return new self(null, $attributes, $image);
    }

    public static function restore(CardId $id, CardAttributes $attributes, ?CardImage $image): self
    {
        return new self($id, $attributes, $image);
    }

    public function change(CardAttributes $attributes, ?CardImage $image): self
    {
        return new self($this->id, $attributes, $image);
    }

    public function id(): ?CardId
    {
        return $this->id;
    }

    public function nameEn(): CardName
    {
        return $this->attributes->nameEn;
    }

    public function namePt(): ?CardName
    {
        return $this->attributes->namePt;
    }

    public function gameId(): GameId
    {
        return $this->attributes->gameId;
    }

    public function editionId(): EditionId
    {
        return $this->attributes->editionId;
    }

    public function rarityId(): RarityId
    {
        return $this->attributes->rarityId;
    }

    public function image(): ?CardImage
    {
        return $this->image;
    }
}
