<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220719082500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'upload_batch\', \'upload_percent\', \'upload_result\' and \'upload_status\' for Log Sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `log_sessions` 
                ADD COLUMN `upload_batch` LONGTEXT NULL AFTER `operatorID`,
                ADD COLUMN `upload_percent` TINYINT(3) UNSIGNED NULL AFTER `upload_batch`,
                ADD COLUMN `upload_result` TEXT NULL AFTER `upload_percent`,
                ADD COLUMN `upload_status` ENUM('Pending', 'Processing', 'Uploaded') NULL DEFAULT 'Uploaded' AFTER `upload_result`;
EOD;
        $this->addSql($sql);

        $sql = "UPDATE `log_sessions` SET `upload_percent` = 100, `upload_status` = 'Uploaded'";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `log_sessions` 
                DROP COLUMN `upload_batch`,
                DROP COLUMN `upload_percent`,
                DROP COLUMN `upload_result`,
                DROP COLUMN `upload_status`;
EOD;
        $this->addSql($sql);
    }
}
