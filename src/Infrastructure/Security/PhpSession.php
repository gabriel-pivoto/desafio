<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

final class PhpSession
{
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name('cartas_session');
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function regenerate(): void
    {
        $this->start();
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $this->start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $this->expireCookie();
        }

        session_destroy();
    }

    private function expireCookie(): void
    {
        $parameters = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parameters['path'],
            'domain' => $parameters['domain'],
            'secure' => $parameters['secure'],
            'httponly' => $parameters['httponly'],
            'samesite' => $parameters['samesite'] ?: 'Lax',
        ]);
    }
}
