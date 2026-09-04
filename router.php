<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (str_starts_with($path, '/api')) {
    require __DIR__ . '/public/api/index.php';

    return true;
}

if ($path === '/cartas' || $path === '/cartas/') {
    require __DIR__ . '/public/cartas.html';

    return true;
}

$file = __DIR__ . '/public' . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/public/index.html';

return true;
