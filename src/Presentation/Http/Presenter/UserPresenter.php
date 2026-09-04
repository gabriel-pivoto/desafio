<?php

declare(strict_types=1);

namespace App\Presentation\Http\Presenter;

use App\Domain\Identity\User;

final class UserPresenter
{
    public function present(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->id()->value(),
            'name' => $user->name(),
            'email' => $user->email()->value(),
        ];
    }
}
