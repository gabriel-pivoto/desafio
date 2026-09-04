<?php

declare(strict_types=1);

namespace App\Domain\Card;

use App\Domain\Shared\Exception\ValidationFailed;

final class CardImage
{
    private string $path;

    public function __construct(string $path)
    {
        if (trim($path) === '') {
            throw ValidationFailed::withMessage('Caminho de imagem inválido.');
        }

        $this->path = $path;
    }

    public static function fromNullable(?string $path): ?self
    {
        return $path === null || trim($path) === '' ? null : new self($path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function equals(?self $other): bool
    {
        return $other !== null && $other->path === $this->path;
    }
}
