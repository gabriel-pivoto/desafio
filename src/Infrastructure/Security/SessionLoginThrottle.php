<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Port\LoginThrottle;
use App\Domain\Shared\Exception\TooManyAttempts;

final class SessionLoginThrottle implements LoginThrottle
{
    private const KEY = 'login_failures';
    private const MAX_FAILURES = 5;
    private const LOCK_SECONDS = 60;

    public function __construct(private PhpSession $session)
    {
    }

    public function ensureNotLocked(): void
    {
        $state = $this->session->get(self::KEY);

        if (!is_array($state) || ($state['count'] ?? 0) < self::MAX_FAILURES) {
            return;
        }

        $elapsed = time() - (int) ($state['at'] ?? 0);

        if ($elapsed < self::LOCK_SECONDS) {
            throw TooManyAttempts::retryAfter(self::LOCK_SECONDS - $elapsed);
        }

        $this->clear();
    }

    public function registerFailure(): void
    {
        $state = $this->session->get(self::KEY);
        $count = is_array($state) ? (int) ($state['count'] ?? 0) : 0;

        $this->session->put(self::KEY, ['count' => $count + 1, 'at' => time()]);
    }

    public function clear(): void
    {
        $this->session->forget(self::KEY);
    }
}
