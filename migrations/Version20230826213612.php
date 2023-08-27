<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230826213612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flashcard DROP FOREIGN KEY FK_70511A09F675F31B');
        $this->addSql('DROP INDEX IDX_70511A09F675F31B ON flashcard');
        $this->addSql('ALTER TABLE flashcard CHANGE author_id unit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE flashcard ADD CONSTRAINT FK_70511A09F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('CREATE INDEX IDX_70511A09F8BD700D ON flashcard (unit_id)');
        $this->addSql('ALTER TABLE unit ADD topic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C531F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('CREATE INDEX IDX_DCBB0C531F55203D ON unit (topic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flashcard DROP FOREIGN KEY FK_70511A09F8BD700D');
        $this->addSql('DROP INDEX IDX_70511A09F8BD700D ON flashcard');
        $this->addSql('ALTER TABLE flashcard CHANGE unit_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE flashcard ADD CONSTRAINT FK_70511A09F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_70511A09F675F31B ON flashcard (author_id)');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C531F55203D');
        $this->addSql('DROP INDEX IDX_DCBB0C531F55203D ON unit');
        $this->addSql('ALTER TABLE unit DROP topic_id');
    }
}
