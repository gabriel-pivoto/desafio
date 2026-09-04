<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class AuthenticationFailed extends DomainError
{
    public static function invalidCredentials(): self
    {
        return new self('E-mail ou senha inválidos.');
    }

    public static function sessionExpired(): self
    {
        return new self('Sessão expirada. Faça login novamente.');
    }
}
