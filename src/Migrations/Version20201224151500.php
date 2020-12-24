<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224151500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `first_log` DATETIME AFTER `administratorID`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `last_log`  DATETIME AFTER `first_log`');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `first_log`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `last_log`');
    }
}
