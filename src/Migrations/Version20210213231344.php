<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210213231344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE logs SET LSB_approx=\'\'  WHERE LSB_approx IS NULL');
        $this->addSql('UPDATE logs SET LSB_approx=\'~\' WHERE LSB_approx = \'1\'');
        $this->addSql('UPDATE logs SET USB_approx=\'\'  WHERE USB_approx IS NULL');
        $this->addSql('UPDATE logs SET USB_approx=\'~\' WHERE USB_approx = \'1\'');
        $this->addSql('ALTER TABLE logs CHANGE LSB_approx LSB_approx CHAR(1) NOT NULL, CHANGE USB_approx USB_approx CHAR(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logs CHANGE LSB_approx LSB_approx CHAR(1) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE USB_approx USB_approx CHAR(1) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
    }
}
