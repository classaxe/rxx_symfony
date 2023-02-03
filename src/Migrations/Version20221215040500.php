<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221215040500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Modifies CLE table to allow for new saved status codes';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `rxx`.`cle` 
                CHANGE COLUMN `world_range_1_active` `world_range_1_active` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `world_range_2_active` `world_range_2_active` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_1_active` `europe_range_1_active` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_2_active` `europe_range_2_active` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL;
EOD;
        $this->addSql($sql);

    }

    public function down(Schema $schema) : void
    {
        $sql = <<< EOD
            ALTER TABLE `rxx`.`cle` 
                CHANGE COLUMN `world_range_1_active` `world_range_1_active` ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `world_range_2_active` `world_range_2_active` ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_1_active` `europe_range_1_active` ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_2_active` `europe_range_2_active` ENUM('', '1', '2') NULL DEFAULT NULL;
EOD;
        $this->addSql($sql);
    }
}
