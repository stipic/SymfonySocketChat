<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190319201249 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE `sessions` (
            `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `sess_data` BLOB NOT NULL,
            `sess_time` INTEGER UNSIGNED NOT NULL,
            `sess_lifetime` MEDIUMINT NOT NULL
        ) COLLATE utf8_bin, ENGINE = InnoDB;');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, role VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_F06D397057698A6A (role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, deleted TINYINT(1) NOT NULL, messageBlock INT DEFAULT NULL, INDEX IDX_DB021E96B66A53E4 (messageBlock), INDEX IDX_DB021E96DE12AB56 (created_by), INDEX IDX_DB021E9616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversations (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, channel_name VARCHAR(255) DEFAULT \'\', created_at DATETIME NOT NULL, is_channel TINYINT(1) NOT NULL, is_channel_public TINYINT(1) DEFAULT \'0\', deleted TINYINT(1) NOT NULL, conversationNameForOwner INT DEFAULT NULL, conversationNameForGuest INT DEFAULT NULL, INDEX IDX_C2521BF1ADF70A15 (conversationNameForOwner), INDEX IDX_C2521BF1CE20765C (conversationNameForGuest), INDEX IDX_C2521BF1DE12AB56 (created_by), INDEX IDX_C2521BF116FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, message INT DEFAULT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, file_size VARCHAR(255) NOT NULL, INDEX IDX_6354059B6BD307F (message), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, display_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_conversation (user_id INT NOT NULL, conversation_id INT NOT NULL, INDEX IDX_A425AEBA76ED395 (user_id), INDEX IDX_A425AEB9AC0396 (conversation_id), PRIMARY KEY(user_id, conversation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_message (user_id INT NOT NULL, message_id INT NOT NULL, INDEX IDX_EEB02E75A76ED395 (user_id), INDEX IDX_EEB02E75537A1329 (message_id), PRIMARY KEY(user_id, message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_blocks (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, conversation INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F9CA0877DE12AB56 (created_by), INDEX IDX_F9CA087716FE72E1 (updated_by), INDEX IDX_F9CA08778A8E26E9 (conversation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE craue_config_setting (name VARCHAR(255) NOT NULL, section VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96B66A53E4 FOREIGN KEY (messageBlock) REFERENCES message_blocks (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9616FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1ADF70A15 FOREIGN KEY (conversationNameForOwner) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1CE20765C FOREIGN KEY (conversationNameForGuest) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF116FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059B6BD307F FOREIGN KEY (message) REFERENCES messages (id)');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA0877DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA087716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message_blocks ADD CONSTRAINT FK_F9CA08778A8E26E9 FOREIGN KEY (conversation) REFERENCES conversations (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059B6BD307F');
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75537A1329');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEB9AC0396');
        $this->addSql('ALTER TABLE message_blocks DROP FOREIGN KEY FK_F9CA08778A8E26E9');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96DE12AB56');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9616FE72E1');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1ADF70A15');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1CE20765C');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1DE12AB56');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF116FE72E1');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEBA76ED395');
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75A76ED395');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE message_blocks DROP FOREIGN KEY FK_F9CA0877DE12AB56');
        $this->addSql('ALTER TABLE message_blocks DROP FOREIGN KEY FK_F9CA087716FE72E1');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96B66A53E4');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_conversation');
        $this->addSql('DROP TABLE user_message');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE message_blocks');
        $this->addSql('DROP TABLE craue_config_setting');
        $this->addSql('DROP TABLE sessions');
    }
}
