<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

final class Rarity
{
    public function __construct(
        private RarityId $id,
        private string $slug,
        private string $name,
    ) {
    }

    public function id(): RarityId
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
