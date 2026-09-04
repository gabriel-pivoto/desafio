<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Domain\Identity\User;
use App\Domain\Shared\Exception\AuthenticationFailed;

final class RequireAuthenticatedUser
{
    public function __construct(private GetCurrentUser $currentUser)
    {
    }

    public function __invoke(): User
    {
        return ($this->currentUser)() ?? throw AuthenticationFailed::sessionExpired();
    }
}
