<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use App\Domain\Shared\Exception\ValidationFailed;

final class HashedPassword
{
    private string $value;

    public function __construct(string $value)
    {
        if (trim($value) === '') {
            throw ValidationFailed::withMessage('Senha armazenada inválida.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
