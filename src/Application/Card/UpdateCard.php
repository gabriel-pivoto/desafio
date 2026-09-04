<?php

declare(strict_types=1);

namespace App\Application\Card;

use App\Application\Card\ReadModel\CardView;
use App\Application\Port\ImageStorage;
use App\Domain\Card\CardId;
use App\Domain\Card\CardImage;
use App\Domain\Card\CardRepository;
use App\Domain\Shared\Exception\EntityNotFound;
use Throwable;

final class UpdateCard
{
    public function __construct(
        private CardRepository $cards,
        private CardInputValidator $validator,
        private ImageStorage $images,
        private GetCardView $view,
    ) {
    }

    public function __invoke(int $id, CardInput $input): CardView
    {
        $cardId = new CardId($id);
        $current = $this->cards->ofId($cardId) ?? throw EntityNotFound::card();
        $attributes = $this->validator->validate($input);

        $previous = $current->image();
        $image = $this->resolveImage($input, $previous);
        $uploaded = $input->image === null ? null : $image;

        try {
            $this->cards->save($current->change($attributes, $image));
        } catch (Throwable $error) {
            $this->images->remove($uploaded);

            throw $error;
        }

        if (!$this->sameImage($previous, $image)) {
            $this->images->remove($previous);
        }

        return ($this->view)($id);
    }

    private function resolveImage(CardInput $input, ?CardImage $previous): ?CardImage
    {
        if ($input->image !== null) {
            return $this->images->store($input->image);
        }

        return $input->removeImage ? null : $previous;
    }

    private function sameImage(?CardImage $previous, ?CardImage $current): bool
    {
        return $previous === null ? $current === null : $previous->equals($current);
    }
}
