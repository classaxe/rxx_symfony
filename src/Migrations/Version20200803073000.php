<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200803073000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD logon_count int unsigned NOT NULL default 0 AFTER log_latest, ADD logon_latest datetime AFTER logon_count');
        $this->addSql('ALTER TABLE users DROP log_earliest, DROP log_latest');
        $this->addSql('ALTER TABLE users CHANGE admin access int unsigned NOT NULL default 0');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP logon_count, DROP logon_latest');
        $this->addSql('ALTER TABLE users ADD log_earliest DATE DEFAULT NULL AFTER email, ADD log_latest DATE DEFAULT NULL AFTER log_earliest');
        $this->addSql('ALTER TABLE users CHANGE access admin int unsigned NOT NULL default 0');
    }
}
