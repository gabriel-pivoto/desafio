<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use App\Application\Card\CardInput;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\UploadedImageFactory;

final class CardInputFactory
{
    public function __construct(private UploadedImageFactory $images)
    {
    }

    public function fromRequest(Request $request): CardInput
    {
        return new CardInput(
            $request->string('name_en'),
            $request->string('name_pt'),
            $request->integer('game_id'),
            $request->integer('edition_id'),
            $request->integer('rarity_id'),
            $this->images->fromRequest($request, 'image'),
            $request->boolean('remove_image'),
        );
    }
}
