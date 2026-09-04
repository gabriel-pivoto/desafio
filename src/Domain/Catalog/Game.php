<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

final class Game
{
    public function __construct(
        private GameId $id,
        private string $slug,
        private string $name,
    ) {
    }

    public function id(): GameId
    {
        return $this->id;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function name(): string
    {
        return $this->name;
    }
}
