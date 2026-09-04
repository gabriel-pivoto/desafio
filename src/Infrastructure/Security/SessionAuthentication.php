<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Port\AuthenticationSession;
use App\Domain\Identity\UserId;

final class SessionAuthentication implements AuthenticationSession
{
    private const KEY = 'authenticated_user_id';

    public function __construct(
        private PhpSession $session,
        private CsrfGuard $csrf,
    ) {
    }

    public function start(UserId $userId): void
    {
        $this->session->regenerate();
        $this->session->put(self::KEY, $userId->value());
        $this->csrf->rotate();
    }

    public function userId(): ?UserId
    {
        $stored = $this->session->get(self::KEY);

        return is_int($stored) ? new UserId($stored) : null;
    }

    public function end(): void
    {
        $this->session->destroy();
    }
}
