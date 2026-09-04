<?php

declare(strict_types=1);

namespace App\Application\Identity;

use App\Domain\Identity\Email;
use App\Domain\Shared\ErrorBag;

final class Credentials
{
    private function __construct(
        public readonly Email $email,
        public readonly string $password,
    ) {
    }

    public static function create(string $email, string $password): self
    {
        $errors = new ErrorBag();
        $errors->addWhen(trim($email) === '', 'email', 'Informe o e-mail.');
        $errors->addWhen($password === '', 'password', 'Informe a senha.');
        $errors->stopWhenNotEmpty('Preencha e-mail e senha.');

        return new self(new Email($email), $password);
    }
}
