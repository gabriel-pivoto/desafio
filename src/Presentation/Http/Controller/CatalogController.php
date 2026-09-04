<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Catalog\GetCatalog;
use App\Application\Catalog\ListEditionsOfGame;
use App\Application\Identity\RequireAuthenticatedUser;
use App\Domain\Shared\Exception\ValidationFailed;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Presentation\Http\Presenter\CatalogPresenter;

final class CatalogController
{
    public function __construct(
        private RequireAuthenticatedUser $requireUser,
        private GetCatalog $catalog,
        private ListEditionsOfGame $editions,
        private CatalogPresenter $presenter,
    ) {
    }

    public function index(Request $request): Response
    {
        ($this->requireUser)();

        return Response::ok($this->presenter->presentCatalog(($this->catalog)()));
    }

    public function editions(Request $request): Response
    {
        ($this->requireUser)();

        $game = $request->query('game');

        if ($game === '') {
            throw ValidationFailed::withMessage('Informe o card game para listar as edições.');
        }

        return Response::ok([
            'editions' => $this->presenter->presentEditions(($this->editions)($game)),
        ]);
    }
}
