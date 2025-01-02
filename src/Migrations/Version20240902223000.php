<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220122754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new field "about" for CLE definition';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cle ADD `about` TEXT DEFAULT NULL');
        $this->addSql('UPDATE cle SET `about` =
    "Co-ordinated Listening Events (or CLEs) take place several times a year and provide opportunities 
    for listeners of all levels of experience to practice our hobby and learn more about it.<br>\n
    To find out more about the %URL1%, or to join (it\'s free!), visit %URL2%"
            LIMIT 1000'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cle DROP `about`');
    }
}
