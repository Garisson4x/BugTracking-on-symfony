<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190829083652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tickets ADD assigned_id INT NOT NULL');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4E1501A05 FOREIGN KEY (assigned_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('CREATE INDEX IDX_54469DF4E1501A05 ON tickets (assigned_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4E1501A05');
        $this->addSql('DROP INDEX IDX_54469DF4E1501A05 ON tickets');
    }
}
