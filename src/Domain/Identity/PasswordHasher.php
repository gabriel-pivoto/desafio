<?php

declare(strict_types=1);

namespace App\Domain\Identity;

interface PasswordHasher
{
    public function matches(string $plainPassword, HashedPassword $hashedPassword): bool;
}
