<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201228090000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added more settings for CLE editor for filtering';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_1_recently` ENUM('', 'logged', 'unlogged') NULL DEFAULT NULL AFTER `world_range_1_sp_itu_clause`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_1_within` TEXT NULL DEFAULT NULL AFTER `world_range_1_recently`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_1_active` ENUM('', '1', '2') NULL DEFAULT NULL AFTER `world_range_1_within`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_2_recently` ENUM('', 'logged', 'unlogged') NULL DEFAULT NULL AFTER `world_range_2_sp_itu_clause`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_2_within` TEXT NULL DEFAULT NULL AFTER `world_range_2_recently`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `world_range_2_active` ENUM('', '1', '2') NULL DEFAULT NULL AFTER `world_range_2_within`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_1_recently` ENUM('', 'logged', 'unlogged') NULL DEFAULT NULL AFTER `europe_range_1_sp_itu_clause`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_1_within` TEXT NULL DEFAULT NULL AFTER `europe_range_1_recently`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_1_active` ENUM('', '1', '2') NULL DEFAULT NULL AFTER `europe_range_1_within`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_2_recently` ENUM('', 'logged', 'unlogged') NULL DEFAULT NULL AFTER `europe_range_2_sp_itu_clause`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_2_within` TEXT NULL DEFAULT NULL AFTER `europe_range_2_recently`");
        $this->addSql("ALTER TABLE `rxx`.`cle`  ADD COLUMN `europe_range_2_active` ENUM('', '1', '2') NULL DEFAULT NULL AFTER `europe_range_2_within`");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_1_recently`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_1_within`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_1_active`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_2_recently`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_2_within`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `world_range_2_active`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_1_recently`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_1_within`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_1_active`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_2_recently`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_2_within`');
        $this->addSql('ALTER TABLE `rxx`.`cle` DROP `europe_range_2_active`');
    }
}
