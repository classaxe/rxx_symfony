<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201226203000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`listeners`  CHANGE COLUMN `count_logsessions` `count_logsessions` INT(10) UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE `rxx`.`listeners`     ADD COLUMN `logsession_latest` DATETIME NULL DEFAULT NULL AFTER `log_latest`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_DGPS` INT(10) UNSIGNED NOT NULL AFTER `logs`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_DSC` INT(10) UNSIGNED NOT NULL AFTER `logs_DGPS`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_HAMBCN` INT(10) UNSIGNED NOT NULL AFTER `logs_DSC`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_NAVTEX` INT(10) UNSIGNED NOT NULL AFTER `logs_HAMBCN`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_NDB` INT(10) UNSIGNED NOT NULL AFTER `logs_NAVTEX`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_OTHER` INT(10) UNSIGNED NOT NULL AFTER `logs_NDB`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions`  ADD COLUMN `logs_TIME` INT(10) UNSIGNED NOT NULL AFTER `logs_OTHER`');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`listeners`  CHANGE COLUMN `count_logsessions` `count_logsessions` INT(1) UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE `rxx`.`listeners`    DROP `logsession_latest`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_DGPS`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_DSC`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_HAMBCN`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_NAVTEX`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_NDB`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_OTHER`');
        $this->addSql('ALTER TABLE `rxx`.`log_sessions` DROP `logs_TIME`');
    }
}
