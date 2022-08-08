<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220808110000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds \'dx_deg\' and index to Log records';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `logs`
                ADD COLUMN `dx_deg` SMALLINT(3) UNSIGNED NULL DEFAULT NULL AFTER `daytime`,
                ADD INDEX `idx_dx_deg` (`dx_deg` ASC);
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `logs` DROP COLUMN `dx_deg` , DROP INDEX `idx_dx_deg`";
        $this->addSql($sql);
    }
}
