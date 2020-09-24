<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200704143129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (ID INT AUTO_INCREMENT NOT NULL, active TINYINT(1) NOT NULL, count_log INT UNSIGNED NOT NULL, count_log_session INT UNSIGNED NOT NULL, email VARCHAR(40) NOT NULL, log_earliest DATE DEFAULT NULL, log_latest DATE DEFAULT NULL, name VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, PRIMARY KEY(ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE region CHANGE name name VARCHAR(50) NOT NULL, CHANGE region region VARCHAR(3) NOT NULL, CHANGE map map VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE places CHANGE capital capital TINYINT(1) NOT NULL, CHANGE itu itu VARCHAR(3) NOT NULL, CHANGE lat lat VARCHAR(7) NOT NULL, CHANGE lon lon VARCHAR(8) NOT NULL, CHANGE name name VARCHAR(40) NOT NULL, CHANGE population population INT NOT NULL, CHANGE sp sp VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE maps CHANGE SP SP CHAR(2) NOT NULL, CHANGE ix1 ix1 INT UNSIGNED NOT NULL, CHANGE ix2 ix2 INT UNSIGNED NOT NULL, CHANGE iy1 iy1 INT UNSIGNED NOT NULL, CHANGE iy2 iy2 INT UNSIGNED NOT NULL, CHANGE region region CHAR(3) NOT NULL, CHANGE ITU ITU CHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE itu CHANGE ITU ITU VARCHAR(3) NOT NULL, CHANGE name name VARCHAR(50) NOT NULL, CHANGE region region VARCHAR(3) NOT NULL, CHANGE hasSp hasSp INT NOT NULL, CHANGE spTitle spTitle VARCHAR(45) NOT NULL');
        $this->addSql('ALTER TABLE signals CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE `call` `call` VARCHAR(12) NOT NULL, CHANGE format format VARCHAR(25) NOT NULL, CHANGE heard_in heard_in VARCHAR(255) DEFAULT NULL, CHANGE heard_in_af heard_in_af TINYINT(1) NOT NULL, CHANGE heard_in_an heard_in_an TINYINT(1) NOT NULL, CHANGE heard_in_as heard_in_as TINYINT(1) NOT NULL, CHANGE heard_in_ca heard_in_ca TINYINT(1) NOT NULL, CHANGE heard_in_eu heard_in_eu TINYINT(1) NOT NULL, CHANGE heard_in_iw heard_in_iw TINYINT(1) NOT NULL, CHANGE heard_in_na heard_in_na TINYINT(1) NOT NULL, CHANGE heard_in_oc heard_in_oc TINYINT(1) NOT NULL, CHANGE heard_in_sa heard_in_sa TINYINT(1) NOT NULL, CHANGE khz khz NUMERIC(9, 3) DEFAULT \'0.000\' NOT NULL, CHANGE pwr pwr DOUBLE PRECISION NOT NULL, CHANGE region region VARCHAR(2) NOT NULL, CHANGE sec sec VARCHAR(12) NOT NULL, CHANGE type type CHAR(1) NOT NULL');
        $this->addSql('ALTER TABLE sp CHANGE SP SP VARCHAR(3) NOT NULL, CHANGE name name VARCHAR(40) NOT NULL, CHANGE ITU ITU VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE listeners CHANGE callsign callsign VARCHAR(12) NOT NULL, CHANGE count_DGPS count_DGPS INT UNSIGNED NOT NULL, CHANGE count_DSC count_DSC INT UNSIGNED NOT NULL, CHANGE count_HAMBCN count_HAMBCN INT UNSIGNED NOT NULL, CHANGE count_logs count_logs INT UNSIGNED NOT NULL, CHANGE count_NAVTEX count_NAVTEX INT UNSIGNED NOT NULL, CHANGE count_NDB count_NDB INT UNSIGNED NOT NULL, CHANGE count_OTHER count_OTHER INT UNSIGNED NOT NULL, CHANGE count_TIME count_TIME INT UNSIGNED NOT NULL, CHANGE count_signals count_signals INT UNSIGNED NOT NULL, CHANGE email email VARCHAR(40) NOT NULL, CHANGE GSQ GSQ VARCHAR(6) NOT NULL, CHANGE ITU ITU VARCHAR(3) NOT NULL, CHANGE lat lat DOUBLE PRECISION NOT NULL, CHANGE log_earliest log_earliest DATE DEFAULT NULL, CHANGE log_format log_format VARCHAR(255) NOT NULL, CHANGE log_latest log_latest DATE DEFAULT NULL, CHANGE lon lon DOUBLE PRECISION NOT NULL, CHANGE map_x map_x SMALLINT NOT NULL, CHANGE map_y map_y SMALLINT NOT NULL, CHANGE name name VARCHAR(40) NOT NULL, CHANGE notes notes VARCHAR(255) NOT NULL, CHANGE primary_QTH primary_QTH TINYINT(1) NOT NULL, CHANGE QTH QTH VARCHAR(255) NOT NULL, CHANGE region region VARCHAR(2) NOT NULL, CHANGE SP SP VARCHAR(6) NOT NULL, CHANGE timezone timezone VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE poll_question CHANGE active active TINYINT(1) NOT NULL, CHANGE title title VARCHAR(50) NOT NULL, CHANGE text text VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cle CHANGE ID ID INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE cle cle INT UNSIGNED DEFAULT NULL, CHANGE date_timespan date_timespan VARCHAR(255) DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL, CHANGE additional additional VARCHAR(255) DEFAULT NULL, CHANGE world_range_1_low world_range_1_low DOUBLE PRECISION DEFAULT NULL, CHANGE world_range_1_high world_range_1_high DOUBLE PRECISION DEFAULT NULL, CHANGE world_range_2_low world_range_2_low DOUBLE PRECISION DEFAULT NULL, CHANGE world_range_2_high world_range_2_high DOUBLE PRECISION DEFAULT NULL, CHANGE europe_range_1_low europe_range_1_low DOUBLE PRECISION DEFAULT NULL, CHANGE europe_range_1_high europe_range_1_high DOUBLE PRECISION DEFAULT NULL, CHANGE europe_range_2_low europe_range_2_low DOUBLE PRECISION DEFAULT NULL, CHANGE europe_range_2_high europe_range_2_high DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE poll_answer CHANGE questionID questionID INT NOT NULL, CHANGE text text VARCHAR(50) NOT NULL, CHANGE votes votes INT NOT NULL');
        $this->addSql('ALTER TABLE icao CHANGE ICAO ICAO VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE logs CHANGE signalID signalID INT NOT NULL, CHANGE daytime daytime TINYINT(1) NOT NULL, CHANGE format format VARCHAR(25) NOT NULL, CHANGE heard_in heard_in VARCHAR(3) NOT NULL, CHANGE sec sec VARCHAR(12) NOT NULL, CHANGE time time VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE attachment CHANGE ID ID BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE destinationID destinationID BIGINT UNSIGNED NOT NULL, CHANGE destinationTable destinationTable VARCHAR(20) NOT NULL, CHANGE size size BIGINT UNSIGNED NOT NULL, CHANGE title title VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE attachment CHANGE ID ID INT AUTO_INCREMENT NOT NULL, CHANGE destinationID destinationID BIGINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE destinationTable destinationTable VARCHAR(20) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE size size BIGINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE title title VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE type type VARCHAR(20) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE cle CHANGE ID ID INT AUTO_INCREMENT NOT NULL, CHANGE cle cle INT UNSIGNED DEFAULT 0, CHANGE date_timespan date_timespan VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' COLLATE `utf8_general_ci`, CHANGE scope scope VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' COLLATE `utf8_general_ci`, CHANGE additional additional TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, CHANGE world_range_1_low world_range_1_low DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE world_range_1_high world_range_1_high DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE world_range_2_low world_range_2_low DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE world_range_2_high world_range_2_high DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE europe_range_1_low europe_range_1_low DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE europe_range_1_high europe_range_1_high DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE europe_range_2_low europe_range_2_low DOUBLE PRECISION UNSIGNED DEFAULT \'0\', CHANGE europe_range_2_high europe_range_2_high DOUBLE PRECISION UNSIGNED DEFAULT \'0\'');
        $this->addSql('ALTER TABLE icao CHANGE ICAO ICAO VARCHAR(4) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE itu CHANGE ITU ITU VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE name name VARCHAR(50) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE region region VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE hasSp hasSp INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE spTitle spTitle VARCHAR(45) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE listeners CHANGE callsign callsign VARCHAR(12) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE count_DGPS count_DGPS INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_DSC count_DSC INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_HAMBCN count_HAMBCN INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_logs count_logs INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_NAVTEX count_NAVTEX INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_NDB count_NDB INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_OTHER count_OTHER INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_TIME count_TIME INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE count_signals count_signals INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE email email VARCHAR(40) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE GSQ GSQ VARCHAR(6) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE ITU ITU VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE lat lat DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE log_earliest log_earliest DATE DEFAULT \'0000-00-00\' NOT NULL, CHANGE log_format log_format VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE log_latest log_latest DATE DEFAULT \'0000-00-00\' NOT NULL, CHANGE lon lon DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE map_x map_x SMALLINT DEFAULT 0 NOT NULL, CHANGE map_y map_y SMALLINT DEFAULT 0 NOT NULL, CHANGE name name VARCHAR(40) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE notes notes VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE primary_QTH primary_QTH TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE QTH QTH VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE region region VARCHAR(2) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE SP SP VARCHAR(6) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE timezone timezone VARCHAR(5) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE logs CHANGE signalID signalID INT DEFAULT 0 NOT NULL, CHANGE daytime daytime TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE format format VARCHAR(25) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE heard_in heard_in VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE sec sec VARCHAR(12) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE time time VARCHAR(5) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE maps CHANGE SP SP CHAR(2) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE ix1 ix1 INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE ix2 ix2 INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE iy1 iy1 INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE iy2 iy2 INT UNSIGNED DEFAULT 0 NOT NULL, CHANGE region region CHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE ITU ITU CHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE places CHANGE capital capital TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE itu itu VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE lat lat VARCHAR(7) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE lon lon VARCHAR(8) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE name name VARCHAR(40) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE population population INT DEFAULT 0 NOT NULL, CHANGE sp sp VARCHAR(2) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE poll_answer CHANGE questionID questionID INT DEFAULT 0 NOT NULL, CHANGE text text VARCHAR(50) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE votes votes INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE poll_question CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE title title VARCHAR(50) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE text text VARCHAR(255) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE region CHANGE map map VARCHAR(10) CHARACTER SET utf8 DEFAULT \'\' COLLATE `utf8_general_ci`, CHANGE name name VARCHAR(50) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE region region VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE signals CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE `call` `call` VARCHAR(12) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE format format VARCHAR(25) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE heard_in heard_in TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, CHANGE heard_in_af heard_in_af TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_an heard_in_an TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_as heard_in_as TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_ca heard_in_ca TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_eu heard_in_eu TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_iw heard_in_iw TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_na heard_in_na TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_oc heard_in_oc TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE heard_in_sa heard_in_sa TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE khz khz NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE pwr pwr DOUBLE PRECISION UNSIGNED DEFAULT \'0\' NOT NULL, CHANGE region region VARCHAR(2) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE sec sec VARCHAR(12) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE type type CHAR(1) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
        $this->addSql('ALTER TABLE sp CHANGE SP SP VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE name name VARCHAR(40) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, CHANGE ITU ITU VARCHAR(3) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`');
    }
}