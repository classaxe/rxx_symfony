<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220618114500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'CLE settings - additional filters';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`cle` 
                CHANGE COLUMN `date_start` `date_start` DATE NULL DEFAULT NULL,
                CHANGE COLUMN `date_end`   `date_end`   DATE NULL DEFAULT NULL,
                CHANGE COLUMN `world_range_1_channels`  `world_range_1_channels`  ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `world_range_2_channels`  `world_range_2_channels`  ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_1_channels` `europe_range_1_channels` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_2_channels` `europe_range_2_channels` ENUM('', '1', '2', '3', '4') NULL DEFAULT NULL;
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`cle` 
                CHANGE COLUMN `world_range_1_channels`  `world_range_1_channels`  ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `world_range_2_channels`  `world_range_2_channels`  ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_1_channels` `europe_range_1_channels` ENUM('', '1', '2') NULL DEFAULT NULL,
                CHANGE COLUMN `europe_range_2_channels` `europe_range_2_channels` ENUM('', '1', '2') NULL DEFAULT NULL;
EOD;
    }
}
