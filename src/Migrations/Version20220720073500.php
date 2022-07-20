<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720073500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'upload_count\', \'upload_cursor\' and \'upload_stats\' and extends \'comment\' for Log Sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `log_sessions` 
                ADD COLUMN `upload_count` INT(5) UNSIGNED NULL AFTER `upload_batch`,
                ADD COLUMN `upload_cursor` INT(5) UNSIGNED NULL AFTER `upload_count`,
                ADD COLUMN `upload_stats` TEXT NULL NULL AFTER `upload_result`,
                CHANGE COLUMN `comment` `comment` VARCHAR(255) NULL DEFAULT NULL;
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `log_sessions` 
                DROP COLUMN `upload_count`,
                DROP COLUMN `upload_cursor`,
                DROP COLUMN `upload_stats`,
                CHANGE COLUMN `comment` `comment` VARCHAR(20) NULL DEFAULT NULL;;
EOD;
        $this->addSql($sql);
    }
}
