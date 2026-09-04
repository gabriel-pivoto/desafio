<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Shared\Exception\AccessDenied;
use App\Infrastructure\Http\Request;

final class CsrfGuard
{
    private const KEY = 'csrf_token';
    private const HEADER = 'X-CSRF-Token';
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function __construct(private PhpSession $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->put(self::KEY, $token);
        }

        return $token;
    }

    public function rotate(): string
    {
        $this->session->forget(self::KEY);

        return $this->token();
    }

    public function verify(Request $request): void
    {
        if (in_array($request->rawMethod(), self::SAFE_METHODS, true)) {
            return;
        }

        $sent = $request->header(self::HEADER) ?? $request->string('_csrf');

        if ($sent === '' || !hash_equals($this->token(), $sent)) {
            throw AccessDenied::invalidCsrfToken();
        }
    }
}
