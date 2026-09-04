<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class ValidationFailed extends DomainError
{
    private array $errors;

    private function __construct(string $message, array $errors)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public static function withMessage(string $message): self
    {
        return new self($message, []);
    }

    public static function withErrors(array $errors, string $message = 'Revise os campos destacados.'): self
    {
        return new self($message, $errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
