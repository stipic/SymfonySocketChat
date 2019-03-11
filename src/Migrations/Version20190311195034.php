<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190311195034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE message_blocks (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, conversation INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F9CA0877DE12AB56 (created_by), INDEX IDX_F9CA087716FE72E1 (updated_by), INDEX IDX_F9CA08778A8E26E9 (conversation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA0877DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA087716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA08778A8E26E9 FOREIGN KEY (conversation) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE conversations CHANGE is_channel_public is_channel_public TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E968A8E26E9');
        $this->addSql('DROP INDEX IDX_DB021E968A8E26E9 ON messages');
        $this->addSql('ALTER TABLE messages CHANGE conversation messageBlock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96B66A53E4 FOREIGN KEY (messageBlock) REFERENCES message_blocks (id)');
        $this->addSql('CREATE INDEX IDX_DB021E96B66A53E4 ON messages (messageBlock)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96B66A53E4');
        $this->addSql('DROP TABLE message_blocks');
        $this->addSql('ALTER TABLE conversations CHANGE is_channel_public is_channel_public TINYINT(1) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_DB021E96B66A53E4 ON messages');
        $this->addSql('ALTER TABLE messages CHANGE messageblock conversation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E968A8E26E9 FOREIGN KEY (conversation) REFERENCES conversations (id)');
        $this->addSql('CREATE INDEX IDX_DB021E968A8E26E9 ON messages (conversation)');
    }
}
