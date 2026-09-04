<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class TooManyAttempts extends DomainError
{
    public static function retryAfter(int $seconds): self
    {
        return new self(sprintf('Muitas tentativas seguidas. Tente novamente em %d segundos.', $seconds));
    }
}
