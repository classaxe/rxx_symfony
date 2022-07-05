<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220705141000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds WWSU integration fields for listeners';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`listeners` 
                ADD COLUMN `wwsu_enable` char(1) NOT NULL DEFAULT 'N' AFTER `website`,
                ADD COLUMN `wwsu_key` varchar(20) NOT NULL DEFAULT '' AFTER `wwsu_enable`,
                ADD COLUMN `wwsu_perm_cycle` char(1) NOT NULL DEFAULT 'N' AFTER `wwsu_key`,
                ADD COLUMN `wwsu_perm_offsets` char(1) NOT NULL DEFAULT 'N' AFTER `wwsu_perm_cycle`;
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`listeners` 
                DROP COLUMN `wwsu_enable`,
                DROP COLUMN `wwsu_key`,
                DROP COLUMN `wwsu_perm_cycle`,
                DROP COLUMN `wwsu_perm_offsets`;
EOD;
        $this->addSql($sql);
    }
}
