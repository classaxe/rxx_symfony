<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220804450000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'signals\' count and index to Log Sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `log_sessions`
                ADD COLUMN `signals` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `operatorID`,
                ADD INDEX `idx_signals` (`signals` ASC);
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `log_sessions` DROP COLUMN `signals` , DROP INDEX `idx_signals`";
        $this->addSql($sql);
    }
}
