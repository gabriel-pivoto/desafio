<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Port\UploadedImage;
use App\Domain\Shared\Exception\ValidationFailed;

final class UploadedImageFactory
{
    private const SIZE_ERRORS = [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE];

    public function fromRequest(Request $request, string $field): ?UploadedImage
    {
        $file = $request->file($field);

        if ($file === null) {
            return null;
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if (in_array($error, self::SIZE_ERRORS, true)) {
            throw ValidationFailed::withErrors([$field => 'A imagem deve ter no máximo 2 MB.']);
        }

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            throw ValidationFailed::withErrors([$field => 'Não foi possível enviar a imagem. Tente novamente.']);
        }

        return new UploadedImage((string) $file['tmp_name'], (int) ($file['size'] ?? 0));
    }
}
