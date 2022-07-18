<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201220064500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Dropped \'heard_in_rna\' column and index from signals';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`signals`  DROP INDEX `heard_in_rna`');
        $this->addSql('ALTER TABLE `rxx`.`signals`  DROP `heard_in_rna`');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `rxx`.`signals`  ADD `heard_in_rna` tinyint(1) NOT NULL AFTER heard_in_sa');
        $this->addSql('ALTER TABLE `rxx`.`signals`  ADD INDEX `heard_in_rna` (`heard_in_rna`)');
    }
}
