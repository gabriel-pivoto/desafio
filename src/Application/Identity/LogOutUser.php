<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Application\Port\AuthenticationSession;

final class LogOutUser
{
    public function __construct(private AuthenticationSession $session)
    {
    }

    public function __invoke(): void
    {
        $this->session->end();
    }
}
