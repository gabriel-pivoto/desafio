<?php

declare(strict_types=1);

namespace App\Domain\Identity;

interface UserRepository
{
    public function ofEmail(Email $email): ?User;

    public function ofId(UserId $id): ?User;
}
