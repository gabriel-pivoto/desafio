<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Domain\Card\CardAttributes;
use App\Domain\Card\CardName;
use App\Domain\Catalog\EditionId;
use App\Domain\Catalog\EditionRepository;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\GameRepository;
use App\Domain\Catalog\RarityId;
use App\Domain\Catalog\RarityRepository;
use App\Domain\Shared\ErrorBag;

final class CardInputValidator
{
    public function __construct(
        private GameRepository $games,
        private EditionRepository $editions,
        private RarityRepository $rarities,
    ) {
    }

    public function validate(CardInput $input): CardAttributes
    {
        $errors = new ErrorBag();

        $this->checkNames($errors, $input);
        $game = $this->checkGame($errors, $input->gameId);
        $rarity = $this->checkRarity($errors, $input->rarityId);
        $edition = $this->checkEdition($errors, $input->editionId, $game);

        $errors->stopWhenNotEmpty();

        return new CardAttributes(
            new CardName($input->nameEn),
            CardName::optional($input->namePt),
            $game,
            $edition,
            $rarity,
        );
    }

    private function checkNames(ErrorBag $errors, CardInput $input): void
    {
        $errors->addWhen(trim($input->nameEn) === '', 'name_en', 'Informe o nome da carta em inglês.');
        $errors->addWhen(
            mb_strlen(trim($input->nameEn)) > CardName::MAX_LENGTH,
            'name_en',
            'O nome em inglês deve ter no máximo ' . CardName::MAX_LENGTH . ' caracteres.',
        );
        $errors->addWhen(
            mb_strlen(trim($input->namePt)) > CardName::MAX_LENGTH,
            'name_pt',
            'O nome em português deve ter no máximo ' . CardName::MAX_LENGTH . ' caracteres.',
        );
    }

    private function checkGame(ErrorBag $errors, ?int $gameId): ?GameId
    {
        if ($gameId === null || $gameId <= 0) {
            $errors->add('game_id', 'Selecione o card game.');

            return null;
        }

        $game = $this->games->ofId(new GameId($gameId));

        if ($game === null) {
            $errors->add('game_id', 'Card game inválido.');

            return null;
        }

        return $game->id();
    }

    private function checkRarity(ErrorBag $errors, ?int $rarityId): ?RarityId
    {
        if ($rarityId === null || $rarityId <= 0) {
            $errors->add('rarity_id', 'Selecione a raridade da carta.');

            return null;
        }

        $rarity = new RarityId($rarityId);

        if (!$this->rarities->exists($rarity)) {
            $errors->add('rarity_id', 'Raridade inválida.');

            return null;
        }

        return $rarity;
    }

    private function checkEdition(ErrorBag $errors, ?int $editionId, ?GameId $gameId): ?EditionId
    {
        if ($editionId === null || $editionId <= 0) {
            $errors->add('edition_id', 'Selecione a edição da carta.');

            return null;
        }

        $edition = new EditionId($editionId);

        if ($gameId !== null && !$this->editions->belongsToGame($edition, $gameId)) {
            $errors->add('edition_id', 'Esta edição não pertence ao card game escolhido.');

            return null;
        }

        return $edition;
    }
}
