<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class AccessDenied extends DomainError
{
    public static function invalidCsrfToken(): self
    {
        return new self('Token de segurança inválido. Recarregue a página e tente novamente.');
    }
}
