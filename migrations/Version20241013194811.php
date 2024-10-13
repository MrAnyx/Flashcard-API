<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013194811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session RENAME COLUMN startedat TO started_at');
        $this->addSql('ALTER TABLE session RENAME COLUMN endedat TO ended_at');
        $this->addSql('ALTER TABLE "user" DROP premium');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD premium BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE session RENAME COLUMN started_at TO startedat');
        $this->addSql('ALTER TABLE session RENAME COLUMN ended_at TO endedat');
    }
}
