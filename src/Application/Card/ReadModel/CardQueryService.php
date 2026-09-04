<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

use App\Domain\Card\CardCriteria;
use App\Domain\Card\CardId;

interface CardQueryService
{
    public function paginate(CardCriteria $criteria): CardViewPage;

    public function ofId(CardId $id): ?CardView;

    public function summary(): CardSummary;
}
