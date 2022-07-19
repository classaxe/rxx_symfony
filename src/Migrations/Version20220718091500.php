<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718091500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'count_remote_logsesions\' for listeners, \'comment\' and an index for Log Sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` ADD COLUMN `count_remote_logsessions` INT(10) UNSIGNED NOT NULL AFTER `count_remote_logs`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `log_sessions` ADD COLUMN `comment` VARCHAR(20) NULL DEFAULT NULL AFTER `administratorID`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `log_sessions` ADD INDEX `idx_operatorID` (`operatorID` ASC);";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `log_sessions` DROP INDEX `idx_operatorID`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `log_sessions` DROP COLUMN `comment`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `listeners` DROP COLUMN `count_remote_logsessions`;";
        $this->addSql($sql);
    }
}
