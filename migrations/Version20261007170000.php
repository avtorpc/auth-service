<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261007170000 extends AbstractMigration {
 public function getDescription(): string { return 'One role per email account, prototype profile and optional phone'; }
 public function up(Schema $schema): void {
  $s='"'.str_replace('"','""',$_ENV['DB_SCHEMA'] ?? 'public').'"';
  $this->addSql("ALTER TABLE {$s}.users ALTER phone_number DROP NOT NULL, ADD role_code VARCHAR(50) NOT NULL DEFAULT 'applicant' CHECK(role_code IN ('applicant','employer')), ADD profile JSONB NOT NULL DEFAULT '{}'");
  $this->addSql("CREATE UNIQUE INDEX users_email_normalized ON {$s}.users (lower(email))");
 }
 public function down(Schema $schema): void { $this->throwIrreversibleMigrationException('Email-only accounts cannot restore mandatory phone.'); }
}
