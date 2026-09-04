<?php

declare(strict_types=1);

namespace App\Domain\Identity;

final class User
{
    public function __construct(
        private UserId $id,
        private string $name,
        private Email $email,
        private HashedPassword $password,
    ) {
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }
}
