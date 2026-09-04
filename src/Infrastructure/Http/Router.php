<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = ['method' => $method, 'pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(Request $request): Response
    {
        $path = rtrim($request->path(), '/') ?: '/';
        $pathMatched = false;

        foreach ($this->routes as $route) {
            $regex = '#^' . preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route['pattern']) . '$#';

            if (preg_match($regex, $path, $matches) !== 1) {
                continue;
            }

            $pathMatched = true;

            if ($route['method'] !== $request->method()) {
                continue;
            }

            array_shift($matches);

            return ($route['handler'])($request, ...$matches);
        }

        throw $pathMatched ? HttpError::methodNotAllowed() : HttpError::notFound();
    }
}
