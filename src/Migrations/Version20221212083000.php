<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221212083000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'Decommissioned\' and index to signals';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `signals`
                ADD COLUMN `decommissioned` tinyint(1) NULL DEFAULT NULL AFTER `call`,
                ADD INDEX `idx_decommissioned` (`decommissioned`);
EOD;
        $this->addSql($sql);

        $sql = <<< EOD
            UPDATE `signals` SET `decommissioned` = 0;
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `signals` DROP COLUMN `decommissioned` , DROP INDEX `idx_decommissioned`";
        $this->addSql($sql);
    }
}
