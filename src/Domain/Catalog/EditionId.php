<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\Shared\IntegerId;

final class EditionId extends IntegerId
{
    protected static function label(): string
    {
        return 'Edição';
    }
}
