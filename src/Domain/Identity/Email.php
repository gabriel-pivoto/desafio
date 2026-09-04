<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use App\Domain\Shared\Exception\ValidationFailed;

final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if ($normalized === '' || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw ValidationFailed::withErrors(['email' => 'Informe um e-mail válido.']);
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
