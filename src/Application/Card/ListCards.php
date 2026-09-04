<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Card\ReadModel\CardQueryService;
use App\Application\Card\ReadModel\CardViewPage;
use App\Domain\Card\CardCriteria;

final class ListCards
{
    public function __construct(private CardQueryService $cards)
    {
    }

    public function __invoke(CardCriteria $criteria): CardViewPage
    {
        return $this->cards->paginate($criteria);
    }
}
