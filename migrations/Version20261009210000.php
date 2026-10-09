<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261009210000 extends AbstractMigration
{
    public function getDescription(): string { return 'Persistent independent browser sessions, revoked explicitly on logout'; }
    public function up(Schema $schema): void
    {
        $name = $_ENV['DB_SCHEMA'] ?? 'auth';
        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier($name).'.refresh_tokens';
        $this->addSql("ALTER TABLE {$table} ALTER COLUMN expires_at DROP NOT NULL");
        $this->addSql("ALTER TABLE {$table} DROP CONSTRAINT uq_refresh_tokens_user_id");
        // Preserve active logins but never reactivate an expired or revoked credential.
        $this->addSql("UPDATE {$table} SET expires_at = NULL WHERE revoked_at IS NULL AND expires_at > (CURRENT_TIMESTAMP AT TIME ZONE 'UTC')");
        $this->addSql("CREATE UNIQUE INDEX uq_refresh_token_hash ON {$table} (refresh_token_hash)");
    }
    public function down(Schema $schema): void { $this->throwIrreversibleMigrationException('Do not discard independent browser sessions.'); }
}
