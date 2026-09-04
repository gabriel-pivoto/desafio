<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class EntityNotFound extends DomainError
{
    public static function card(): self
    {
        return new self('Carta não encontrada.');
    }

    public static function game(): self
    {
        return new self('Card game não encontrado.');
    }
}
