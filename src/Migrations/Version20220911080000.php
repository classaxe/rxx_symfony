<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220911080000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'formatted_location\' and index to listeners';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `listeners`
                ADD COLUMN `formatted_location` varchar(255) NULL DEFAULT NULL AFTER `equipment`,
                ADD INDEX `idx_formatted_location` (`formatted_location` ASC);
EOD;
        $this->addSql($sql);

        $sql = <<< EOD
            UPDATE `listeners` SET `formatted_location` = IF(
                name IS NULL,
                '',
                CONCAT(
                    name,
                    ' | ',
                    qth,
                    ' ',
                    IF(sp != '', CONCAT(sp, ' '), ''),
                    itu,
                    ' ',
                    gsq
                )
            );
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` DROP COLUMN `formatted_location` , DROP INDEX `idx_formatted_location`";
        $this->addSql($sql);
    }
}
