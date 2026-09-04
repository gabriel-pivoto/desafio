<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\Shared\IntegerId;

final class RarityId extends IntegerId
{
    protected static function label(): string
    {
        return 'Raridade';
    }
}
