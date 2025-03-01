<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227090649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP rating, CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_3A10012D1136BE75 ON series');
        $this->addSql('ALTER TABLE series DROP character_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD rating DOUBLE PRECISION DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE series ADD character_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_3A10012D1136BE75 ON series (character_id)');
    }
}
