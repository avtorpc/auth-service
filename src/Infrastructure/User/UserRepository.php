<?php

namespace App\Infrastructure\User;

use App\Domain\User\User;
use App\Infrastructure\DB\SchemaSqlHelper;
use App\Shared\Time\SystemClock;
use Doctrine\DBAL\Connection;

final class UserRepository
{
    private const TABLE = 'users';

    public function __construct(
        private Connection $connection,
        private readonly SchemaSqlHelper $schemaSqlHelper,
        private SystemClock $clock
    ) {}

    public function findByEmail(string $email): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
             FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
             WHERE email = :email
             LIMIT 1',
            ['email' => $email]
        );

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findById(int $id): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
             FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
             WHERE id = :id
             LIMIT 1',
            ['id' => $id]
        );

        return $row ? $this->mapRowToUser($row) : null;
    }


    public function save(User $user): int
    {
        $table = $this->schemaSqlHelper->table(self::TABLE);

        $this->connection->insert($table, [
            'user_uuid' => $user->getUuid(),
            'email' => $user->getEmail(),
            'last_name' => $user->getLastName(),
            'first_name' => $user->getFirstName(),
            'patronymic' => $user->getPatronymic(),
            'phone_number' => $user->getPhoneNumber(),
            'verification_channel_id' => $user->getVerificationChannelId(),
            'password_hash' => $user->getPasswordHash(),
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * ROW → DOMAIN
     */
    private function mapRowToUser(array $row): User
    {
        return new User(
            id: (int) ($row['id'] ?? 0),
            uuid: (string) ($row['user_uuid'] ?? ''),
            email: (string) ($row['email'] ?? ''),
            passwordHash: (string) ($row['password_hash'] ?? ''),
            firstName: (string) ($row['first_name'] ?? ''),
            lastName: (string) ($row['last_name'] ?? ''),
            patronymic: $row['patronymic'] ?? null,
            phoneNumber: $row['phone_number'] ?? null,
            verificationChannelId: $row['verification_channel_id'] ?? null,
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
            roles: ['ROLE_USER']
        );
    }
    public function findByUuid(string $uuid): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
         FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
         WHERE user_uuid = :uuid
         LIMIT 1',
            ['uuid' => $uuid]
        );

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function findByPhone(string $phone): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
         FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
         WHERE phone_number = :phone
         LIMIT 1',
            ['phone' => $phone]
        );

        return $row ? $this->mapRowToUser($row) : null;
    }

    public function getName(): string
    {
        return 'user-db-repository';
    }
}
