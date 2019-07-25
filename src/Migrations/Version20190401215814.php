<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401215814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9693CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB021E9693CB796C ON messages (file_id)');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059B6BD307F');
        $this->addSql('DROP INDEX IDX_6354059B6BD307F ON files');
        $this->addSql('ALTER TABLE files DROP message');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files ADD message INT DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059B6BD307F FOREIGN KEY (message) REFERENCES messages (id)');
        $this->addSql('CREATE INDEX IDX_6354059B6BD307F ON files (message)');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9693CB796C');
        $this->addSql('DROP INDEX UNIQ_DB021E9693CB796C ON messages');
        $this->addSql('ALTER TABLE messages DROP file_id');
    }
}
