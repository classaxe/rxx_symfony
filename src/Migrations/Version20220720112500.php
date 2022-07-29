<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720112500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Renames \'upload_batch\' to \'upload_entries\' for Log Sessions';
    }

    public function up(Schema $schema) : void
    {
        $sql = "ALTER TABLE `log_sessions` CHANGE COLUMN `upload_batch` `upload_entries` LONGTEXT NULL;";
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = "ALTER TABLE `log_sessions` CHANGE COLUMN `upload_entries` `upload_batch` LONGTEXT NULL;";
        $this->addSql($sql);
    }
}
