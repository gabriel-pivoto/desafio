<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Port\ImageStorage;
use App\Domain\Card\CardId;
use App\Domain\Card\CardRepository;
use App\Domain\Shared\Exception\EntityNotFound;

final class DeleteCard
{
    public function __construct(
        private CardRepository $cards,
        private ImageStorage $images,
    ) {
    }

    public function __invoke(int $id): void
    {
        $cardId = new CardId($id);
        $card = $this->cards->ofId($cardId) ?? throw EntityNotFound::card();

        $this->cards->remove($cardId);
        $this->images->remove($card->image());
    }
}
