<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class Configuration
{
    private array $values;

    public function __construct(string $projectRoot)
    {
        $this->values = [
            'debug' => self::env('APP_ENV', 'local') === 'local',
            'database' => [
                'host' => self::env('DB_HOST', '127.0.0.1'),
                'port' => (int) self::env('DB_PORT', '3306'),
                'name' => self::env('DB_NAME', 'cartas'),
                'user' => self::env('DB_USER', 'cartas'),
                'pass' => self::env('DB_PASS', 'cartas'),
            ],
            'uploads' => [
                'dir' => $projectRoot . '/public/uploads',
                'public_prefix' => '/uploads/',
                'max_bytes' => 2 * 1024 * 1024,
                'allowed_mimes' => [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    'image/gif' => 'gif',
                ],
            ],
        ];
    }

    public function debug(): bool
    {
        return $this->values['debug'];
    }

    public function database(): array
    {
        return $this->values['database'];
    }

    public function uploads(): array
    {
        return $this->values['uploads'];
    }

    private static function env(string $key, string $default): string
    {
        $value = getenv($key);

        return $value === false || $value === '' ? $default : $value;
    }
}
