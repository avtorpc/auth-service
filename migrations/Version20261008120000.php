<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261008120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove obsolete company/SMS registration tables; preserve current accounts and email registration';
    }

    public function up(Schema $schema): void
    {
        $schema = $this->connection->quoteSingleIdentifier($_ENV['DB_SCHEMA'] ?? 'auth');
        $this->addSql("DROP TABLE IF EXISTS {$schema}.users_companies_link");
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Restore the pre-cleanup backup to recover legacy tables and data.');
    }
}
