<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Application\Port\AuthenticationSession;
use App\Domain\Identity\User;
use App\Domain\Identity\UserRepository;

final class GetCurrentUser
{
    public function __construct(
        private UserRepository $users,
        private AuthenticationSession $session,
    ) {
    }

    public function __invoke(): ?User
    {
        $userId = $this->session->userId();

        if ($userId === null) {
            return null;
        }

        $user = $this->users->ofId($userId);

        if ($user === null) {
            $this->session->end();
        }

        return $user;
    }
}
