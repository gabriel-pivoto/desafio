<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use RuntimeException;

final class HttpError extends RuntimeException
{
    private function __construct(private int $status, string $message)
    {
        parent::__construct($message);
    }

    public static function notFound(): self
    {
        return new self(404, 'Endpoint não encontrado.');
    }

    public static function methodNotAllowed(): self
    {
        return new self(405, 'Método não permitido para este endereço.');
    }

    public function status(): int
    {
        return $this->status;
    }
}
