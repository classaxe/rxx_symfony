<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220717131000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Changes listener.primary_QTH from int 0 or 1 to char \'N\' or \'Y\'';
    }

    public function up(Schema $schema) : void
    {
        $sql = "ALTER TABLE `listeners` CHANGE COLUMN `primary_QTH` `primary_QTH` char(1) NOT NULL DEFAULT 'N';";
        $this->addSql($sql);

        $sql = "UPDATE `listeners` SET `primary_QTH`='N' WHERE `primary_QTH`='0';";
        $this->addSql($sql);

        $sql = "UPDATE `listeners` SET `primary_QTH`='Y' WHERE `primary_QTH`='1';";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "UPDATE `listeners` SET `primary_QTH`='0' WHERE `primary_QTH`='N';";
        $this->addSql($sql);

        $sql = "UPDATE `listeners` SET `primary_QTH`='1' WHERE `primary_QTH`='Y';";
        $this->addSql($sql);

        $sql = "ALTER TABLE `listeners` CHANGE COLUMN `primary_QTH` `primary_QTH` TINYINT(1) UNSIGNED NOT NULL;";
        $this->addSql($sql);
    }
}
