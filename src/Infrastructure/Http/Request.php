<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class Request
{
    private const METHOD_OVERRIDES = ['PUT', 'PATCH', 'DELETE'];

    private function __construct(
        private string $method,
        private string $path,
        private array $query,
        private array $body,
        private array $files,
    ) {
    }

    public static function capture(): self
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

        if (str_starts_with($path, '/api')) {
            $path = substr($path, 4);
        }

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            '/' . trim($path, '/'),
            $_GET,
            self::readBody(),
            $_FILES,
        );
    }

    private static function readBody(): array
    {
        $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));

        if (!str_contains($contentType, 'application/json')) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        $decoded = $raw === false || $raw === '' ? null : json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function method(): string
    {
        $override = strtoupper((string) ($this->body['_method'] ?? ''));

        return $this->method === 'POST' && in_array($override, self::METHOD_OVERRIDES, true)
            ? $override
            : $this->method;
    }

    public function rawMethod(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, string $default = ''): string
    {
        $value = $this->query[$key] ?? null;

        return is_scalar($value) && (string) $value !== '' ? trim((string) $value) : $default;
    }

    public function queryInt(string $key, int $default = 0): int
    {
        $value = $this->query($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    public function string(string $key, string $default = ''): string
    {
        $value = $this->body[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    public function integer(string $key): ?int
    {
        $value = $this->body[$key] ?? null;

        return $value === null || $value === '' || !is_numeric($value) ? null : (int) $value;
    }

    public function boolean(string $key): bool
    {
        return filter_var($this->body[$key] ?? false, FILTER_VALIDATE_BOOL);
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return isset($_SERVER[$key]) ? (string) $_SERVER[$key] : null;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE ? $file : null;
    }
}
