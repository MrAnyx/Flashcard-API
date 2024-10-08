<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240814211311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flashcard (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, front VARCHAR(255) NOT NULL, back VARCHAR(255) NOT NULL, details VARCHAR(1000) DEFAULT NULL, next_review TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, previous_review TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, state INT NOT NULL, difficulty DOUBLE PRECISION DEFAULT NULL, stability DOUBLE PRECISION DEFAULT NULL, favorite BOOLEAN NOT NULL, unit_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_70511A09F8BD700D ON flashcard (unit_id)');
        $this->addSql('CREATE TABLE password_reset (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(128) NOT NULL, used BOOLEAN NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1017252A76ED395 ON password_reset (user_id)');
        $this->addSql('CREATE TABLE review (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, grade INT NOT NULL, reset BOOLEAN NOT NULL, flashcard_id INT DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6C5D16576 ON review (flashcard_id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
        $this->addSql('CREATE TABLE setting (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(1000) NOT NULL, type VARCHAR(255) NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9F74B898A76ED395 ON setting (user_id)');
        $this->addSql('CREATE TABLE topic (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(35) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(300) NOT NULL, favorite BOOLEAN NOT NULL, author_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9D40DE1BF675F31B ON topic (author_id)');
        $this->addSql('CREATE TABLE unit (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(35) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(300) NOT NULL, favorite BOOLEAN NOT NULL, topic_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DCBB0C531F55203D ON unit (topic_id)');
        $this->addSql('CREATE TABLE "user" (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(30) NOT NULL, token VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, premium BOOLEAN NOT NULL, premium_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE flashcard ADD CONSTRAINT FK_70511A09F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B1017252A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6C5D16576 FOREIGN KEY (flashcard_id) REFERENCES flashcard (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C531F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flashcard DROP CONSTRAINT FK_70511A09F8BD700D');
        $this->addSql('ALTER TABLE password_reset DROP CONSTRAINT FK_B1017252A76ED395');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6C5D16576');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE setting DROP CONSTRAINT FK_9F74B898A76ED395');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1BF675F31B');
        $this->addSql('ALTER TABLE unit DROP CONSTRAINT FK_DCBB0C531F55203D');
        $this->addSql('DROP TABLE flashcard');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
