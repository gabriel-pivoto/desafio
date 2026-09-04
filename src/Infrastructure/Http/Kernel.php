<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Infrastructure\Security\CsrfGuard;
use Throwable;

final class Kernel
{
    public function __construct(
        private Router $router,
        private CsrfGuard $csrf,
        private ErrorResponder $responder,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $this->csrf->verify($request);

            return $this->router->dispatch($request);
        } catch (Throwable $error) {
            return $this->responder->toResponse($error);
        }
    }
}
