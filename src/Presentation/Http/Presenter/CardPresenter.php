<?php

declare(strict_types=1);

namespace App\Presentation\Http\Presenter;

use App\Application\Card\ReadModel\CardSummary;
use App\Application\Card\ReadModel\CardView;
use App\Application\Card\ReadModel\CardViewPage;
use App\Application\Card\ReadModel\GameTally;
use App\Application\Card\ReadModel\TaggedItem;

final class CardPresenter
{
    public function present(CardView $card): array
    {
        return [
            'id' => $card->id,
            'name_en' => $card->nameEn,
            'name_pt' => $card->namePt,
            'image_path' => $card->imagePath,
            'created_at' => $card->createdAt,
            'updated_at' => $card->updatedAt,
            'game' => $this->tagged($card->game, 'slug'),
            'edition' => $this->tagged($card->edition, 'code'),
            'rarity' => $this->tagged($card->rarity, 'slug'),
        ];
    }

    public function presentPage(CardViewPage $page): array
    {
        return [
            'items' => array_map($this->present(...), $page->items),
            'total' => $page->total,
            'page' => $page->page,
            'per_page' => $page->perPage,
            'pages' => $page->pages(),
        ];
    }

    public function presentSummary(CardSummary $summary): array
    {
        return [
            'total' => $summary->total,
            'by_game' => array_map(
                static fn (GameTally $tally): array => [
                    'slug' => $tally->slug,
                    'name' => $tally->name,
                    'total' => $tally->total,
                ],
                $summary->byGame,
            ),
        ];
    }

    private function tagged(TaggedItem $item, string $codeKey): array
    {
        return [
            'id' => $item->id,
            $codeKey => $item->code,
            'name' => $item->name,
        ];
    }
}
