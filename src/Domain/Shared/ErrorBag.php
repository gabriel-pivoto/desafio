<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Domain\Shared\Exception\ValidationFailed;

final class ErrorBag
{
    private array $errors = [];

    public function add(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;

        return $this;
    }

    public function addWhen(bool $condition, string $field, string $message): self
    {
        return $condition ? $this->add($field, $message) : $this;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function isEmpty(): bool
    {
        return $this->errors === [];
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function stopWhenNotEmpty(string $message = 'Revise os campos destacados.'): void
    {
        if (!$this->isEmpty()) {
            throw ValidationFailed::withErrors($this->errors, $message);
        }
    }
}
