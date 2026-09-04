<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

final class Edition
{
    public function __construct(
        private EditionId $id,
        private GameId $gameId,
        private string $code,
        private string $name,
    ) {
    }

    public function id(): EditionId
    {
        return $this->id;
    }

    public function gameId(): GameId
    {
        return $this->gameId;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }
}
