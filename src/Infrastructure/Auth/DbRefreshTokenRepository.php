<?php

namespace App\Infrastructure\Auth;

use App\Shared\Time\ClockInterface;
use App\Infrastructure\DB\SchemaSqlHelper;
use Doctrine\DBAL\Connection;

final class DbRefreshTokenRepository
{
    private const TABLE = 'refresh_tokens';

    public function __construct(
        private Connection $connection,
        private readonly SchemaSqlHelper $schemaSqlHelper,
        private ClockInterface $clock,
    ) {}

    public function save(
        int|string $userId,
        string $tokenHash,
        \DateTimeImmutable $expiresAt,
    ): void {
        $now = $this->clock->now();
        $table = $this->schemaSqlHelper->table(self::TABLE);

        // 1. удалить старый refresh token (1 активная сессия)
        $this->connection->executeStatement(
            "DELETE FROM {$table} WHERE user_id = :userId",
            [
                'userId' => $userId,
            ]
        );

        // 2. вставить новый
        $this->connection->insert(
            $table,
            [
                'user_id' => $userId,
                'refresh_token_hash' => $tokenHash,

                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'revoked_at' => null,

                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function findByHash(string $tokenHash): ?array
    {
        $table = $this->schemaSqlHelper->table(self::TABLE);

        $row = $this->connection->fetchAssociative(
            "SELECT
            id,
            user_id,
            refresh_token_hash,
            expires_at,
            revoked_at,
            created_at,
            updated_at
         FROM {$table}
         WHERE refresh_token_hash = :hash
         LIMIT 1",
            [
                'hash' => $tokenHash,
            ]
        );

        if (!$row) {
            return null;
        }

        return $row;
    }

    public function rotate(
        int|string $userId,
        string $oldHash,
        string $newHash,
        \DateTimeImmutable $expiresAt
    ): void {
        $now = $this->clock->now();
        $table = $this->schemaSqlHelper->table(self::TABLE);

        // 1. обновляем существующую запись (НЕ insert)
        $affected = $this->connection->executeStatement(
            "UPDATE {$table}
         SET refresh_token_hash = :newHash,
             expires_at = :expiresAt,
             revoked_at = NULL,
             updated_at = :now
         WHERE user_id = :userId
           AND refresh_token_hash = :oldHash",
            [
                'userId' => $userId,
                'oldHash' => $oldHash,
                'newHash' => $newHash,
                'expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
                'now' => $now->format('Y-m-d H:i:s'),
            ]
        );

        if ($affected === 0) {
            throw new \RuntimeException('Refresh token not found for rotate');
        }
    }
}
