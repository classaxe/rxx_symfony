<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220716165000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds remote log count for listener';
    }

    public function up(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` ADD COLUMN `count_remote_logs` INT(10) UNSIGNED NOT NULL AFTER `count_TIME`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `logs` ADD INDEX `idx_operatorID` (`operatorID` ASC);";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` DROP COLUMN `count_remote_logs`;";
        $this->addSql($sql);
    }
}
