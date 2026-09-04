<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Card\CardImage;

interface ImageStorage
{
    public function store(UploadedImage $image): CardImage;

    public function remove(?CardImage $image): void;
}
