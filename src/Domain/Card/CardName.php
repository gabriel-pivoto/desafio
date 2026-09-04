<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Shared\Exception\ValidationFailed;

final class CardName
{
    public const MAX_LENGTH = 180;

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '' || mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw ValidationFailed::withMessage('Nome de carta inválido.');
        }

        $this->value = $trimmed;
    }

    public static function optional(string $value): ?self
    {
        return trim($value) === '' ? null : new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
