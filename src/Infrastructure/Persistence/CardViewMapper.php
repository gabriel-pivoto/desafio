<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Card\ReadModel\CardView;
use App\Application\Card\ReadModel\TaggedItem;

final class CardViewMapper
{
    public function map(array $row): CardView
    {
        $namePt = $row['name_pt'] === null ? null : (string) $row['name_pt'];

        return new CardView(
            (int) $row['id'],
            (string) $row['name_en'],
            $namePt === '' ? null : $namePt,
            $row['image_path'] === null ? null : (string) $row['image_path'],
            (string) $row['created_at'],
            (string) $row['updated_at'],
            new TaggedItem((int) $row['game_id'], (string) $row['game_slug'], (string) $row['game_name']),
            new TaggedItem((int) $row['edition_id'], (string) $row['edition_code'], (string) $row['edition_name']),
            new TaggedItem((int) $row['rarity_id'], (string) $row['rarity_slug'], (string) $row['rarity_name']),
        );
    }
}
