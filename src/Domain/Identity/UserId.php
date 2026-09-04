<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use App\Domain\Shared\IntegerId;

final class UserId extends IntegerId
{
    protected static function label(): string
    {
        return 'Usuário';
    }
}
