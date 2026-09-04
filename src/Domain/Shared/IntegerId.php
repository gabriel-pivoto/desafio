<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Domain\Shared\Exception\ValidationFailed;

abstract class IntegerId
{
    private int $value;

    final public function __construct(int $value)
    {
        if ($value <= 0) {
            throw ValidationFailed::withMessage(static::label() . ' inválido.');
        }

        $this->value = $value;
    }

    public static function fromNullable(?int $value): ?static
    {
        return $value === null ? null : new static($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(?self $other): bool
    {
        return $other !== null && $other::class === static::class && $other->value === $this->value;
    }

    abstract protected static function label(): string;
}
