<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Card\CardInputValidator;
use App\Application\Card\CreateCard;
use App\Application\Card\DeleteCard;
use App\Application\Card\GetCardView;
use App\Application\Card\ListCards;
use App\Application\Card\SummariseCards;
use App\Application\Card\UpdateCard;
use App\Application\Catalog\GetCatalog;
use App\Application\Catalog\ListEditionsOfGame;
use App\Application\Identity\AuthenticateUser;
use App\Application\Identity\GetCurrentUser;
use App\Application\Identity\LogOutUser;
use App\Application\Identity\RequireAuthenticatedUser;
use App\Infrastructure\Config\Configuration;
use App\Infrastructure\Http\ErrorResponder;
use App\Infrastructure\Http\Kernel;
use App\Infrastructure\Http\Router;
use App\Infrastructure\Http\UploadedImageFactory;
use App\Infrastructure\Persistence\CardViewMapper;
use App\Infrastructure\Persistence\Connection;
use App\Infrastructure\Persistence\PdoCardQueryService;
use App\Infrastructure\Persistence\PdoCardRepository;
use App\Infrastructure\Persistence\PdoEditionRepository;
use App\Infrastructure\Persistence\PdoGameRepository;
use App\Infrastructure\Persistence\PdoRarityRepository;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Infrastructure\Security\BcryptPasswordHasher;
use App\Infrastructure\Security\CsrfGuard;
use App\Infrastructure\Security\PhpSession;
use App\Infrastructure\Security\SessionAuthentication;
use App\Infrastructure\Security\SessionLoginThrottle;
use App\Infrastructure\Storage\FilesystemImageStorage;
use App\Presentation\Http\CardInputFactory;
use App\Presentation\Http\Controller\AuthController;
use App\Presentation\Http\Controller\CardController;
use App\Presentation\Http\Controller\CatalogController;
use App\Presentation\Http\Presenter\CardPresenter;
use App\Presentation\Http\Presenter\CatalogPresenter;
use App\Presentation\Http\Presenter\UserPresenter;
use App\Presentation\Http\Routes;

final class Container
{
    private array $instances = [];

    public function __construct(private Configuration $config)
    {
    }

    public function kernel(): Kernel
    {
        return $this->share(Kernel::class, function (): Kernel {
            $router = new Router();
            Routes::register($router, $this->authController(), $this->catalogController(), $this->cardController());

            return new Kernel($router, $this->csrfGuard(), new ErrorResponder($this->config->debug()));
        });
    }

    public function session(): PhpSession
    {
        return $this->share(PhpSession::class, static fn (): PhpSession => new PhpSession());
    }

    private function csrfGuard(): CsrfGuard
    {
        return $this->share(CsrfGuard::class, fn (): CsrfGuard => new CsrfGuard($this->session()));
    }

    private function connection(): Connection
    {
        return $this->share(Connection::class, fn (): Connection => new Connection($this->config->database()));
    }

    private function authController(): AuthController
    {
        $authentication = new SessionAuthentication($this->session(), $this->csrfGuard());
        $users = new PdoUserRepository($this->connection());
        $currentUser = new GetCurrentUser($users, $authentication);

        return new AuthController(
            new AuthenticateUser(
                $users,
                new BcryptPasswordHasher(),
                $authentication,
                new SessionLoginThrottle($this->session()),
            ),
            $currentUser,
            new LogOutUser($authentication),
            $this->csrfGuard(),
            new UserPresenter(),
        );
    }

    private function catalogController(): CatalogController
    {
        $games = new PdoGameRepository($this->connection());
        $editions = new PdoEditionRepository($this->connection());

        return new CatalogController(
            $this->requireUser(),
            new GetCatalog($games, new PdoRarityRepository($this->connection())),
            new ListEditionsOfGame($games, $editions),
            new CatalogPresenter(),
        );
    }

    private function cardController(): CardController
    {
        $repository = new PdoCardRepository($this->connection());
        $queries = new PdoCardQueryService($this->connection(), new CardViewMapper());
        $images = new FilesystemImageStorage($this->config->uploads());
        $view = new GetCardView($queries);
        $validator = new CardInputValidator(
            new PdoGameRepository($this->connection()),
            new PdoEditionRepository($this->connection()),
            new PdoRarityRepository($this->connection()),
        );

        return new CardController(
            $this->requireUser(),
            new ListCards($queries),
            new SummariseCards($queries),
            $view,
            new CreateCard($repository, $validator, $images, $view),
            new UpdateCard($repository, $validator, $images, $view),
            new DeleteCard($repository, $images),
            new CardInputFactory(new UploadedImageFactory()),
            new CardPresenter(),
        );
    }

    private function requireUser(): RequireAuthenticatedUser
    {
        return $this->share(RequireAuthenticatedUser::class, fn (): RequireAuthenticatedUser => new RequireAuthenticatedUser(
            new GetCurrentUser(
                new PdoUserRepository($this->connection()),
                new SessionAuthentication($this->session(), $this->csrfGuard()),
            ),
        ));
    }

    private function share(string $key, callable $factory): mixed
    {
        return $this->instances[$key] ??= $factory();
    }
}
