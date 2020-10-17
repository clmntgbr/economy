<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016201007 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station ADD address_id INT DEFAULT NULL, ADD pop VARCHAR(255) NOT NULL, ADD name VARCHAR(255) DEFAULT NULL, ADD element LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD is_closed TINYINT(1) NOT NULL, ADD closed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B3064ACF5B7AF75 ON gas_station (address_id)');
        $this->addSql('ALTER TABLE google_place DROP name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACF5B7AF75');
        $this->addSql('DROP INDEX UNIQ_6B3064ACF5B7AF75 ON gas_station');
        $this->addSql('ALTER TABLE gas_station DROP address_id, DROP pop, DROP name, DROP element, DROP is_closed, DROP closed_at');
        $this->addSql('ALTER TABLE google_place ADD name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
