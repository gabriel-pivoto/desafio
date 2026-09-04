<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Port\ImageStorage;
use App\Application\Port\UploadedImage;
use App\Domain\Card\CardImage;
use App\Domain\Shared\Exception\ValidationFailed;
use finfo;
use RuntimeException;

final class FilesystemImageStorage implements ImageStorage
{
    public function __construct(private array $settings)
    {
    }

    public function store(UploadedImage $image): CardImage
    {
        if ($image->size > $this->settings['max_bytes']) {
            throw ValidationFailed::withErrors(['image' => 'A imagem deve ter no máximo 2 MB.']);
        }

        $extension = $this->extensionOf($image->temporaryPath);
        $this->ensureDirectory();

        $name = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = rtrim($this->settings['dir'], '/\\') . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($image->temporaryPath, $destination)) {
            throw new RuntimeException('Não foi possível gravar a imagem no servidor.');
        }

        chmod($destination, 0644);

        return new CardImage($this->settings['public_prefix'] . $name);
    }

    public function remove(?CardImage $image): void
    {
        if ($image === null || !str_starts_with($image->path(), $this->settings['public_prefix'])) {
            return;
        }

        $file = rtrim($this->settings['dir'], '/\\') . DIRECTORY_SEPARATOR . basename($image->path());

        if (is_file($file)) {
            unlink($file);
        }
    }

    private function extensionOf(string $path): string
    {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if (!is_string($mime) || !isset($this->settings['allowed_mimes'][$mime])) {
            throw ValidationFailed::withErrors(['image' => 'Use um arquivo JPG, PNG, WEBP ou GIF.']);
        }

        return $this->settings['allowed_mimes'][$mime];
    }

    private function ensureDirectory(): void
    {
        $directory = $this->settings['dir'];

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Não foi possível criar a pasta de imagens.');
        }
    }
}
