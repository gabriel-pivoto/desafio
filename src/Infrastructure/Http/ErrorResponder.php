<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Shared\Exception\AccessDenied;
use App\Domain\Shared\Exception\AuthenticationFailed;
use App\Domain\Shared\Exception\EntityNotFound;
use App\Domain\Shared\Exception\TooManyAttempts;
use App\Domain\Shared\Exception\ValidationFailed;
use Throwable;

final class ErrorResponder
{
    public function __construct(private bool $debug)
    {
    }

    public function toResponse(Throwable $error): Response
    {
        return match (true) {
            $error instanceof ValidationFailed => Response::error(422, $error->getMessage(), $error->errors()),
            $error instanceof AuthenticationFailed => Response::error(401, $error->getMessage()),
            $error instanceof AccessDenied => Response::error(403, $error->getMessage()),
            $error instanceof EntityNotFound => Response::error(404, $error->getMessage()),
            $error instanceof TooManyAttempts => Response::error(429, $error->getMessage()),
            $error instanceof HttpError => Response::error($error->status(), $error->getMessage()),
            default => $this->unexpected($error),
        };
    }

    private function unexpected(Throwable $error): Response
    {
        error_log(sprintf('[api] %s: %s', $error::class, $error->getMessage()));

        return Response::error(
            500,
            $this->debug ? 'Erro interno: ' . $error->getMessage() : 'Erro interno no servidor. Tente novamente.',
        );
    }
}
