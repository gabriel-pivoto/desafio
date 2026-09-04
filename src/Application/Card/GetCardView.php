<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Card\ReadModel\CardQueryService;
use App\Application\Card\ReadModel\CardView;
use App\Domain\Card\CardId;
use App\Domain\Shared\Exception\EntityNotFound;

final class GetCardView
{
    public function __construct(private CardQueryService $cards)
    {
    }

    public function __invoke(int $id): CardView
    {
        return $this->cards->ofId(new CardId($id)) ?? throw EntityNotFound::card();
    }
}
