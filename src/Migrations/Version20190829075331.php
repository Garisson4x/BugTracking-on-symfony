<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190829075331 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962AF675F31B ON comments (author_id)');
        $this->addSql('ALTER TABLE projects ADD creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A461220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A461220EA6 ON projects (creator_id)');
        $this->addSql('ALTER TABLE tickets ADD creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF461220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_54469DF461220EA6 ON tickets (creator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AF675F31B');
        $this->addSql('DROP INDEX IDX_5F9E962AF675F31B ON comments');
        $this->addSql('ALTER TABLE comments ADD author VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP author_id');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A461220EA6');
        $this->addSql('DROP INDEX IDX_5C93B3A461220EA6 ON projects');
        $this->addSql('ALTER TABLE projects DROP creator_id');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF461220EA6');
        $this->addSql('DROP INDEX IDX_54469DF461220EA6 ON tickets');
        $this->addSql('ALTER TABLE tickets DROP creator_id');
    }
}
