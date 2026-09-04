<?php

declare(strict_types=1);

namespace App\Presentation\Http\Presenter;

use App\Application\Catalog\Catalog;
use App\Domain\Catalog\Edition;
use App\Domain\Catalog\Game;
use App\Domain\Catalog\Rarity;

final class CatalogPresenter
{
    public function presentCatalog(Catalog $catalog): array
    {
        return [
            'games' => array_map($this->presentGame(...), $catalog->games),
            'rarities' => array_map($this->presentRarity(...), $catalog->rarities),
        ];
    }

    public function presentEditions(array $editions): array
    {
        return array_map($this->presentEdition(...), $editions);
    }

    public function presentGame(Game $game): array
    {
        return [
            'id' => $game->id()->value(),
            'slug' => $game->slug(),
            'name' => $game->name(),
        ];
    }

    public function presentRarity(Rarity $rarity): array
    {
        return [
            'id' => $rarity->id()->value(),
            'slug' => $rarity->slug(),
            'name' => $rarity->name(),
        ];
    }

    public function presentEdition(Edition $edition): array
    {
        return [
            'id' => $edition->id()->value(),
            'code' => $edition->code(),
            'name' => $edition->name(),
        ];
    }
}
