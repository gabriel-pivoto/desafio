<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Shared\IntegerId;

final class CardId extends IntegerId
{
    protected static function label(): string
    {
        return 'Carta';
    }
}
