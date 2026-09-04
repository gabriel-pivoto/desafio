<?php

declare(strict_types=1);

namespace App\Application\Card\ReadModel;

final class CardSummary
{
    public function __construct(
        public readonly int $total,
        public readonly array $byGame,
    ) {
    }

    public static function fromTallies(array $tallies): self
    {
        $total = array_reduce($tallies, static fn (int $carry, GameTally $tally): int => $carry + $tally->total, 0);

        return new self($total, $tallies);
    }
}
