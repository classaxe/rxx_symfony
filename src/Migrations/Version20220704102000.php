<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220704102000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added \'active\' flag for listeners who have retired or died';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`listeners` 
                ADD COLUMN `active` CHAR(1) NULL DEFAULT 'Y' AFTER ID;
EOD;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $sql = <<<EOD
            ALTER TABLE `rxx`.`listeners` 
                DROP COLUMN `active`;
EOD;
        $this->addSql($sql);
    }
}
