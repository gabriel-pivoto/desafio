<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Identity\UserId;

interface AuthenticationSession
{
    public function start(UserId $userId): void;

    public function userId(): ?UserId;

    public function end(): void;
}
