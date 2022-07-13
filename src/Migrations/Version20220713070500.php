<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220713070500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds multi-operator flag for listener locations and operatorID for logs and log_sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` ADD COLUMN `multi_operator` char(1) NOT NULL DEFAULT 'N' AFTER `map_y`";
        $this->addSql($sql);

        $sql = "ALTER TABLE `logs`
                    ADD COLUMN `operatorID` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `LSB_approx`,
                    CHANGE COLUMN `heard_in` `heard_in` VARCHAR(3) NOT NULL AFTER `format`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `log_sessions`
                    ADD COLUMN `operatorID` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `logs_TIME`;";
        $this->addSql($sql);

        $sql = "UPDATE `listeners` SET `multi_operator` = 'Y'
                    WHERE `name` LIKE 'Global Tuner%' OR `name` LIKE 'KIWI SDR%' OR `name` LIKE 'University%'";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `logs` DROP COLUMN `operatorID`,
            CHANGE COLUMN `heard_in` `heard_in` VARCHAR(3) NOT NULL AFTER `LSB_approx`;";
        $this->addSql($sql);

        $sql = "ALTER TABLE `log_sessions` DROP COLUMN `operatorID`";
        $this->addSql($sql);

        $sql = "ALTER TABLE `listeners` DROP COLUMN `multi_operator`";
        $this->addSql($sql);
    }
}
