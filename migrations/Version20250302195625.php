<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302195625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331514956FD FOREIGN KEY (collection_id) REFERENCES collections (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_CBE5A331514956FD ON book (collection_id)');
        $this->addSql('ALTER TABLE collections_series ADD CONSTRAINT FK_39D6E366242C7AD2 FOREIGN KEY (collections_id) REFERENCES collections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collections_series ADD CONSTRAINT FK_39D6E3665278319C FOREIGN KEY (series_id) REFERENCES series (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331514956FD');
        $this->addSql('DROP INDEX IDX_CBE5A331514956FD ON book');
        $this->addSql('ALTER TABLE collections_series DROP FOREIGN KEY FK_39D6E366242C7AD2');
        $this->addSql('ALTER TABLE collections_series DROP FOREIGN KEY FK_39D6E3665278319C');
    }
}
