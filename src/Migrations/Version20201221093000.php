<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201221093000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added Log Sessions support for logs';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`logs`  ADD COLUMN `logSessionID` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `listenerID`');
        $this->addSql('ALTER TABLE `rxx`.`logs`  ADD INDEX `idx_logSessionID` (`logSessionID` ASC);');
        $this->addSql('
            CREATE TABLE `log_sessions` (
              `ID` int(11) NOT NULL AUTO_INCREMENT,
              `timestamp` datetime DEFAULT NULL,
              `administratorID` int(10) unsigned DEFAULT NULL,
              `listenerID` int(10) unsigned DEFAULT NULL,
              `logs` int(10) unsigned DEFAULT NULL,
              PRIMARY KEY (`ID`),
              KEY `idx_timestamp` (`timestamp`),
              KEY `idx_administratorID` (`administratorID`),
              KEY `idx_listenerID` (`listenerID`),
              KEY `idx_logs` (`logs`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE `rxx`.`log_sessions`');
        $this->addSql('ALTER TABLE `rxx`.`logs` DROP INDEX `idx_logSessionID`');
        $this->addSql('ALTER TABLE `rxx`.`logs` DROP `logSessionID`');
    }
}
