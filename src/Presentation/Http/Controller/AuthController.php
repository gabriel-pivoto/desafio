<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Identity\AuthenticateUser;
use App\Application\Identity\Credentials;
use App\Application\Identity\GetCurrentUser;
use App\Application\Identity\LogOutUser;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Security\CsrfGuard;
use App\Presentation\Http\Presenter\UserPresenter;

final class AuthController
{
    public function __construct(
        private AuthenticateUser $authenticate,
        private GetCurrentUser $currentUser,
        private LogOutUser $logOut,
        private CsrfGuard $csrf,
        private UserPresenter $presenter,
    ) {
    }

    public function session(Request $request): Response
    {
        return Response::ok([
            'user' => $this->presenter->present(($this->currentUser)()),
            'csrf_token' => $this->csrf->token(),
        ]);
    }

    public function login(Request $request): Response
    {
        $user = ($this->authenticate)(
            Credentials::create($request->string('email'), $request->string('password')),
        );

        return Response::ok([
            'user' => $this->presenter->present($user),
            'csrf_token' => $this->csrf->token(),
        ]);
    }

    public function logout(Request $request): Response
    {
        ($this->logOut)();

        return Response::ok(['message' => 'Sessão encerrada.']);
    }
}
