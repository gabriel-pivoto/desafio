<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Card\Card;
use App\Domain\Card\CardAttributes;
use App\Domain\Card\CardId;
use App\Domain\Card\CardImage;
use App\Domain\Card\CardName;
use App\Domain\Card\CardRepository;
use App\Domain\Catalog\EditionId;
use App\Domain\Catalog\GameId;
use App\Domain\Catalog\RarityId;

final class PdoCardRepository implements CardRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function ofId(CardId $id): ?Card
    {
        $row = $this->connection->selectOne(
            'SELECT id, name_en, name_pt, game_id, edition_id, rarity_id, image_path FROM cards WHERE id = :id',
            ['id' => $id->value()],
        );

        if ($row === null) {
            return null;
        }

        return Card::restore(
            new CardId((int) $row['id']),
            new CardAttributes(
                new CardName((string) $row['name_en']),
                CardName::optional((string) ($row['name_pt'] ?? '')),
                new GameId((int) $row['game_id']),
                new EditionId((int) $row['edition_id']),
                new RarityId((int) $row['rarity_id']),
            ),
            CardImage::fromNullable($row['image_path'] === null ? null : (string) $row['image_path']),
        );
    }

    public function save(Card $card): CardId
    {
        $values = [
            'name_en' => $card->nameEn()->value(),
            'name_pt' => $card->namePt()?->value(),
            'game_id' => $card->gameId()->value(),
            'edition_id' => $card->editionId()->value(),
            'rarity_id' => $card->rarityId()->value(),
            'image_path' => $card->image()?->path(),
        ];

        $id = $card->id();

        if ($id === null) {
            $this->connection->execute(
                'INSERT INTO cards (name_en, name_pt, game_id, edition_id, rarity_id, image_path)
                 VALUES (:name_en, :name_pt, :game_id, :edition_id, :rarity_id, :image_path)',
                $values,
            );

            return new CardId($this->connection->lastInsertId());
        }

        $this->connection->execute(
            'UPDATE cards SET name_en = :name_en, name_pt = :name_pt, game_id = :game_id,
                    edition_id = :edition_id, rarity_id = :rarity_id, image_path = :image_path
              WHERE id = :id',
            $values + ['id' => $id->value()],
        );

        return $id;
    }

    public function remove(CardId $id): void
    {
        $this->connection->execute('DELETE FROM cards WHERE id = :id', ['id' => $id->value()]);
    }
}
