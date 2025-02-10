<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103152904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metier.lignepagelje ADD code_pagebtgu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.lignepagelje ADD CONSTRAINT FK_97452D2920A1D299 FOREIGN KEY (code_pagebtgu_id) REFERENCES metier.pagebtgu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_97452D2920A1D299 ON metier.lignepagelje (code_pagebtgu_id)');

    }

    public function down(Schema $schema): void
    {

    }
}
