<?php

declare(strict_types=1);

namespace App\Domain\Card;

interface CardRepository
{
    public function ofId(CardId $id): ?Card;

    public function save(Card $card): CardId;

    public function remove(CardId $id): void;
}
