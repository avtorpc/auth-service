<?php

namespace App\Infrastructure\User;

use App\Infrastructure\DB\SchemaSqlHelper;
use Doctrine\DBAL\Connection;

final class UserCompanyLinkRepository
{
    private const TABLE = 'users_companies_link';

    public function __construct(
        private Connection $connection,
        private readonly SchemaSqlHelper $schemaSqlHelper
    ) {}

    public function findByUserUuid(string $userUuid): ?array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
             FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
             WHERE user_uuid = :user_uuid
             LIMIT 1',
            ['user_uuid' => $userUuid]
        );

        return $row ?: null;
    }

    public function findCompanyIdByUserUuid(string $userUuid): ?int
    {
        $companyId = $this->connection->fetchOne(
            'SELECT company_id
             FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
             WHERE user_uuid = :user_uuid
             LIMIT 1',
            ['user_uuid' => $userUuid]
        );

        if ($companyId === false || $companyId === null) {
            return null;
        }

        return (int) $companyId;
    }

    public function findByCompanyId(int $companyId): ?array
    {
        $row = $this->connection->fetchAssociative(
            'SELECT *
             FROM ' . $this->schemaSqlHelper->table(self::TABLE) . '
             WHERE company_id = :company_id
             LIMIT 1',
            ['company_id' => $companyId]
        );

        return $row ?: null;
    }

    public function upsert(string $userUuid, int $companyId): void
    {
        $sql = '
            INSERT INTO ' . $this->schemaSqlHelper->table(self::TABLE) . ' (
                user_uuid,
                company_id,
                source_service,
                created_at,
                updated_at
            )
            VALUES (
                :user_uuid,
                :company_id,
                :source_service,
                NOW(),
                NOW()
            )
            ON CONFLICT (user_uuid)
            DO UPDATE SET
                company_id = EXCLUDED.company_id,
                source_service = EXCLUDED.source_service,
                updated_at = NOW()
        ';

        $this->connection->executeStatement($sql, [
            'user_uuid' => $userUuid,
            'company_id' => $companyId,
            'source_service' => 'verification-service',
        ]);
    }
}
