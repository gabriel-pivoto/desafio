<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Identity\Email;
use App\Domain\Identity\HashedPassword;
use App\Domain\Identity\User;
use App\Domain\Identity\UserId;
use App\Domain\Identity\UserRepository;

final class PdoUserRepository implements UserRepository
{
    private const SELECT = 'SELECT id, name, email, password_hash FROM users';

    public function __construct(private Connection $connection)
    {
    }

    public function ofEmail(Email $email): ?User
    {
        $row = $this->connection->selectOne(
            self::SELECT . ' WHERE email = :email LIMIT 1',
            ['email' => $email->value()],
        );

        return $row === null ? null : $this->toUser($row);
    }

    public function ofId(UserId $id): ?User
    {
        $row = $this->connection->selectOne(
            self::SELECT . ' WHERE id = :id LIMIT 1',
            ['id' => $id->value()],
        );

        return $row === null ? null : $this->toUser($row);
    }

    private function toUser(array $row): User
    {
        return new User(
            new UserId((int) $row['id']),
            (string) $row['name'],
            new Email((string) $row['email']),
            new HashedPassword((string) $row['password_hash']),
        );
    }
}
