<?php

declare(strict_types=1);

namespace App\Application\Port;

final class UploadedImage
{
    public function __construct(
        public readonly string $temporaryPath,
        public readonly int $size,
    ) {
    }
}
