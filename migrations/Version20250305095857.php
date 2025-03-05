<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305095857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, book_id INT DEFAULT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE collections_series ADD CONSTRAINT FK_39D6E366242C7AD2 FOREIGN KEY (collections_id) REFERENCES collections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collections_series ADD CONSTRAINT FK_39D6E3665278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C16A2B381');
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE collections_series DROP FOREIGN KEY FK_39D6E366242C7AD2');
        $this->addSql('ALTER TABLE collections_series DROP FOREIGN KEY FK_39D6E3665278319C');
    }
}
