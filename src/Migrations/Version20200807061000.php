<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807061000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `cle` CHANGE COLUMN `date_timespan` `date_timespan` TEXT NULL DEFAULT NULL, CHANGE COLUMN `scope` `scope` TEXT NULL DEFAULT NULL, CHANGE COLUMN `additional` `additional` TEXT NULL DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `cle` CHANGE COLUMN `date_timespan` `date_timespan` varchar(255) DEFAULT NULL, CHANGE COLUMN `scope` `scope` varchar(255) DEFAULT NULL, CHANGE COLUMN `additional` `additional` varchar(255) DEFAULT NULL');
    }
}
