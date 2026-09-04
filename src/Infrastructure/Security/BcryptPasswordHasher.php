<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Identity\HashedPassword;
use App\Domain\Identity\PasswordHasher;

final class BcryptPasswordHasher implements PasswordHasher
{
    public function matches(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword->value());
    }
}
