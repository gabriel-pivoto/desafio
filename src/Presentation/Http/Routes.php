<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use App\Infrastructure\Http\Router;
use App\Presentation\Http\Controller\AuthController;
use App\Presentation\Http\Controller\CardController;
use App\Presentation\Http\Controller\CatalogController;

final class Routes
{
    public static function register(
        Router $router,
        AuthController $auth,
        CatalogController $catalog,
        CardController $cards,
    ): void {
        $router->get('/auth/me', $auth->session(...));
        $router->post('/auth/login', $auth->login(...));
        $router->post('/auth/logout', $auth->logout(...));

        $router->get('/catalog', $catalog->index(...));
        $router->get('/editions', $catalog->editions(...));

        $router->get('/cards', $cards->index(...));
        $router->post('/cards', $cards->store(...));
        $router->get('/cards/{id}', $cards->show(...));
        $router->put('/cards/{id}', $cards->update(...));
        $router->delete('/cards/{id}', $cards->destroy(...));
    }
}
