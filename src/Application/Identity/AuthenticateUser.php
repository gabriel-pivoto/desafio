<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Application\Port\AuthenticationSession;
use App\Application\Port\LoginThrottle;
use App\Domain\Identity\PasswordHasher;
use App\Domain\Identity\User;
use App\Domain\Identity\UserRepository;
use App\Domain\Shared\Exception\AuthenticationFailed;

final class AuthenticateUser
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private AuthenticationSession $session,
        private LoginThrottle $throttle,
    ) {
    }

    public function __invoke(Credentials $credentials): User
    {
        $this->throttle->ensureNotLocked();

        $user = $this->users->ofEmail($credentials->email);

        if ($user === null || !$this->hasher->matches($credentials->password, $user->password())) {
            $this->throttle->registerFailure();

            throw AuthenticationFailed::invalidCredentials();
        }

        $this->throttle->clear();
        $this->session->start($user->id());

        return $user;
    }
}
