<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Card\CreateCard;
use App\Application\Card\DeleteCard;
use App\Application\Card\GetCardView;
use App\Application\Card\ListCards;
use App\Application\Card\SummariseCards;
use App\Application\Card\UpdateCard;
use App\Application\Identity\RequireAuthenticatedUser;
use App\Domain\Card\CardCriteria;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Presentation\Http\CardInputFactory;
use App\Presentation\Http\Presenter\CardPresenter;

final class CardController
{
    public function __construct(
        private RequireAuthenticatedUser $requireUser,
        private ListCards $listCards,
        private SummariseCards $summarise,
        private GetCardView $getCard,
        private CreateCard $createCard,
        private UpdateCard $updateCard,
        private DeleteCard $deleteCard,
        private CardInputFactory $inputFactory,
        private CardPresenter $presenter,
    ) {
    }

    public function index(Request $request): Response
    {
        ($this->requireUser)();

        $page = ($this->listCards)($this->criteriaFrom($request));

        return Response::ok($this->presenter->presentPage($page) + [
            'summary' => $this->presenter->presentSummary(($this->summarise)()),
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        ($this->requireUser)();

        return Response::ok(['card' => $this->presenter->present(($this->getCard)((int) $id))]);
    }

    public function store(Request $request): Response
    {
        ($this->requireUser)();

        $card = ($this->createCard)($this->inputFactory->fromRequest($request));

        return Response::created([
            'message' => 'Carta cadastrada.',
            'card' => $this->presenter->present($card),
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        ($this->requireUser)();

        $card = ($this->updateCard)((int) $id, $this->inputFactory->fromRequest($request));

        return Response::ok([
            'message' => 'Carta atualizada.',
            'card' => $this->presenter->present($card),
        ]);
    }

    public function destroy(Request $request, string $id): Response
    {
        ($this->requireUser)();

        ($this->deleteCard)((int) $id);

        return Response::ok(['message' => 'Carta excluída.']);
    }

    private function criteriaFrom(Request $request): CardCriteria
    {
        return CardCriteria::create(
            $request->query('search'),
            $request->queryInt('game_id'),
            $request->queryInt('rarity_id'),
            $request->query('sort', 'recent'),
            $request->queryInt('page', 1),
            $request->queryInt('per_page', 10),
        );
    }
}
