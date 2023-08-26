<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824182952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds Donations table';
    }


    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE donations (
                id INT AUTO_INCREMENT NOT NULL,
                date DATE NOT NULL,
                name VARCHAR(50) DEFAULT NULL,
                amount decimal(6,2) DEFAULT NULL,
                message VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql(
            'INSERT INTO donations (date, name, amount, message) VALUES
                ("2020-12-11", "Cornelis Willem Schout", "10.00", ""),
                ("2020-12-14", "Jason Law", "35.00", "To Martin, Wishing you a happy Christmas and all the best for 2021. 73 Jay, 2M0FWQ"),
                ("2021-07-22", "Andrew Price", "100.00", "Thank you for your hard work on the database Martin, best regards - Andy."),
                ("2021-07-22", "Donald Tomkinson", "100.00", ""),
                ("2021-07-29", "Edward Gale", "10.00", ""),
                ("2021-08-25", "Ragnar Östermark", "35.00", ""),
                ("2021-08-29", "Edward Gale", "10.00", ""),
                ("2021-09-29", "Edward Gale", "10.00", ""),
                ("2021-10-29", "Edward Gale", "10.00", ""),
                ("2021-11-02", "Thomas Seeger", "20.00", ""),
                ("2021-11-29", "Edward Gale", "10.00", ""),
                ("2021-12-25", "Andrew Price", "100.00", ""),
                ("2021-12-29", "Edward Gale", "10.00", ""),
                ("2022-01-26", "Stefano Paolini", "10.00", ""),
                ("2022-01-29", "Edward Gale", "10.00", ""),
                ("2022-02-18", "Michael Austin", "10.00", ""),
                ("2022-02-28", "Edward Gale", "10.00", ""),
                ("2022-03-29", "Edward Gale", "10.00", ""),
                ("2022-04-29", "Edward Gale", "10.00", ""),
                ("2022-05-29", "Edward Gale", "10.00", ""),
                ("2022-06-29", "Edward Gale", "10.00", ""),
                ("2022-07-07", "Steven M O\'Kelley", "84.70", "2 m hosting / 1 yr domain registration in CAD"),
                ("2022-07-29", "Edward Gale", "10.00", ""),
                ("2022-08-24", "Jason Law", "20.00", ""),
                ("2022-08-29", "Edward Gale", "10.00", ""),
                ("2022-09-24", "Jason Law", "20.00", ""),
                ("2022-09-29", "Edward Gale", "10.00", ""),
                ("2022-10-24", "Jason Law", "20.00", ""),
                ("2022-10-29", "Edward Gale", "10.00", ""),
                ("2022-11-24", "Jason Law", "20.00", ""),
                ("2022-11-29", "Edward Gale", "10.00", ""),
                ("2022-12-24", "Jason Law", "20.00", ""),
                ("2022-12-29", "Edward Gale", "10.00", ""),
                ("2023-01-24", "Jason Law", "20.00", ""),
                ("2023-01-29", "Edward Gale", "10.00", ""),
                ("2023-02-24", "Jason Law", "20.00", ""),
                ("2023-02-28", "Edward Gale", "10.00", ""),
                ("2023-03-15", "Bertrand Doyon", "20.00", ""),
                ("2023-03-24", "Jason Law", "20.00", ""),
                ("2023-03-29", "Edward Gale", "10.00", ""),
                ("2023-04-24", "Jason Law", "20.00", ""),
                ("2023-04-29", "Edward Gale", "10.00", ""),
                ("2023-05-16", "Zdeněk Čermák", "15.00", ""),
                ("2023-05-24", "Jason Law", "20.00", ""),
                ("2023-05-29", "Edward Gale", "10.00", ""),
                ("2023-06-24", "Jason Law", "20.00", ""),
                ("2023-06-29", "Edward Gale", "10.00", ""),
                ("2023-07-24", "Jason Law", "20.00", ""),
                ("2023-07-29", "Edward Gale", "10.00", ""),
                ("2023-08-24", "Jason Law", "20.00", ""),
                ("2023-08-24", "Donald Tomkinson", "200.00", ""),
                ("2023-08-25", "Thomas Brent", "50.00", "")
            ');

        $this->addSql('
            CREATE TABLE donors (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(50) NOT NULL,
                display VARCHAR(50) NOT NULL,
                email VARCHAR(50) DEFAULT NULL,
                callsign VARCHAR(10) DEFAULT NULL,
                anonymous TINYINT(1) unsigned DEFAULT 0,
                itu VARCHAR(3) DEFAULT NULL,
                sp VARCHAR(2) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'INSERT INTO donors (name, display, callsign, email, anonymous, itu, sp) VALUES
                ("Andrew Price", "Andrew Price", "M0VII", "andy@akprice.net", 0, "ENG", ""),
                ("Bertrand Doyon", "Bertrand Doyon", "", "cuistoswl@yahoo.fr", 0, "FRA", ""),
                ("Cornelis Willem Schout", "Cornelis Willem Schout", "", "scandinavia@telfort.nl", 0, "HOL", ""),
                ("Donald Tomkinson", "Don", "KPC6NDB", "stb55@verizon.net", 0, "USA", "NE"),
                ("Edward Gale", "Alan Gale", "G4TMV", "alan.g4tmv@gmail.com", 0, "ENG", ""),
                ("Jason Law", "Jason Law", "2M0FWQ", "jayandmarie@btinternet.com", 0, "SCT", ""),
                ("Michael Austin", "Michael Austin", "", "exsintaplus@gmail.com", 0, "XXX", ""),
                ("Ragnar Östermark", "Ragnar Östermark", "", "ragnar.ostermark@telia.com", 0, "SWE", ""),
                ("Stefano Paolini", "Stefano Paolini", "IZ2JNN", "iz2jnn@radiomontagna.org", 0, "ITA", ""),
                ("Steven M O\'Kelley", "S M O\'Kelley", "N7IO", "smoketronics@comcast.net", 0, "USA", "WA"),
                ("Thomas Brent", "Tom Brent", "", "navyradiocom@gmail.com", 1, "CAN", "BC"),
                ("Thomas Seeger", "Tom Seeger", "VE3PSZ", "thomas.b.seeger@gmail.com", 0, "CAN", "ON"),
                ("Zdeněk Čermák", "Zdeněk Čermák", "", "zk.cermak@seznam.cz", 0, "CZE", "")
        ');
}

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE donations');
        $this->addSql('DROP TABLE donors');

    }
}
