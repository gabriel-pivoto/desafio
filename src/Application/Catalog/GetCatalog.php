<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\GameRepository;
use App\Domain\Catalog\RarityRepository;

final class GetCatalog
{
    public function __construct(
        private GameRepository $games,
        private RarityRepository $rarities,
    ) {
    }

    public function __invoke(): Catalog
    {
        return new Catalog($this->games->all(), $this->rarities->all());
    }
}
