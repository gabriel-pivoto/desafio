<?php

declare(strict_types=1);

namespace App\Application\Port;

interface LoginThrottle
{
    public function ensureNotLocked(): void;

    public function registerFailure(): void;

    public function clear(): void;
}
