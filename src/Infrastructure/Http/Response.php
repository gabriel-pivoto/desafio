<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class Response
{
    private function __construct(
        private int $status,
        private array $payload,
    ) {
    }

    public static function ok(array $payload): self
    {
        return new self(200, $payload);
    }

    public static function created(array $payload): self
    {
        return new self(201, $payload);
    }

    public static function error(int $status, string $message, array $errors = []): self
    {
        $payload = ['message' => $message];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return new self($status, $payload);
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');

        echo json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
