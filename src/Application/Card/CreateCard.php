<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Card\ReadModel\CardView;
use App\Application\Port\ImageStorage;
use App\Domain\Card\Card;
use App\Domain\Card\CardRepository;
use Throwable;

final class CreateCard
{
    public function __construct(
        private CardRepository $cards,
        private CardInputValidator $validator,
        private ImageStorage $images,
        private GetCardView $view,
    ) {
    }

    public function __invoke(CardInput $input): CardView
    {
        $attributes = $this->validator->validate($input);
        $image = $input->image === null ? null : $this->images->store($input->image);

        try {
            $id = $this->cards->save(Card::register($attributes, $image));
        } catch (Throwable $error) {
            $this->images->remove($image);

            throw $error;
        }

        return ($this->view)($id->value());
    }
}
