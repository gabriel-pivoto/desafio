<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private ?PDO $pdo = null;

    public function __construct(private array $settings)
    {
    }

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->settings['host'],
            $this->settings['port'],
            $this->settings['name'],
        );

        try {
            $this->pdo = new PDO($dsn, $this->settings['user'], $this->settings['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException('Não foi possível conectar ao banco de dados.', 0, $exception);
        }

        return $this->pdo;
    }

    public function select(string $sql, array $parameters = []): array
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function selectOne(string $sql, array $parameters = []): ?array
    {
        return $this->select($sql, $parameters)[0] ?? null;
    }

    public function execute(string $sql, array $parameters = []): int
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($parameters);

        return $statement->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo()->lastInsertId();
    }
}
