<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Card\ReadModel\CardQueryService;
use App\Application\Card\ReadModel\CardSummary;

final class SummariseCards
{
    public function __construct(private CardQueryService $cards)
    {
    }

    public function __invoke(): CardSummary
    {
        return $this->cards->summary();
    }
}
