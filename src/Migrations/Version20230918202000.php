<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918202000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Donation should link via donorId, not donor name';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donations ADD donorID INT DEFAULT NULL');
        $this->addSql('UPDATE donations SET donorID = (SELECT id from donors d where d.name = donations.name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donations DROP donorID');
    }
}
