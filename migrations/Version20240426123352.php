<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426123352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flashcard ADD favorite TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE topic ADD favorite TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE unit ADD favorite TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flashcard DROP favorite');
        $this->addSql('ALTER TABLE topic DROP favorite');
        $this->addSql('ALTER TABLE unit DROP favorite');
    }
}
