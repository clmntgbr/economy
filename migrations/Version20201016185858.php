<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016185858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gas_station_service (service_id INT NOT NULL, station_id INT NOT NULL, INDEX IDX_FD56A06BED5CA9E6 (service_id), INDEX IDX_FD56A06B21BDB235 (station_id), PRIMARY KEY(service_id, station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gas_station_service ADD CONSTRAINT FK_FD56A06BED5CA9E6 FOREIGN KEY (service_id) REFERENCES gas_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station_service ADD CONSTRAINT FK_FD56A06B21BDB235 FOREIGN KEY (station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_price ADD type_id INT DEFAULT NULL, ADD station_id INT DEFAULT NULL, ADD value DOUBLE PRECISION NOT NULL, ADD date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB6C54C8C93 FOREIGN KEY (type_id) REFERENCES gas_type (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB621BDB235 FOREIGN KEY (station_id) REFERENCES gas_station (id)');
        $this->addSql('CREATE INDEX IDX_EEF8FDB6C54C8C93 ON gas_price (type_id)');
        $this->addSql('CREATE INDEX IDX_EEF8FDB621BDB235 ON gas_price (station_id)');
        $this->addSql('ALTER TABLE gas_service ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gas_type ADD name VARCHAR(255) NOT NULL, ADD slug VARCHAR(128) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A29EDA989D9B62 ON gas_type (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE gas_station_service');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB6C54C8C93');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB621BDB235');
        $this->addSql('DROP INDEX IDX_EEF8FDB6C54C8C93 ON gas_price');
        $this->addSql('DROP INDEX IDX_EEF8FDB621BDB235 ON gas_price');
        $this->addSql('ALTER TABLE gas_price DROP type_id, DROP station_id, DROP value, DROP date');
        $this->addSql('ALTER TABLE gas_service DROP name');
        $this->addSql('DROP INDEX UNIQ_8A29EDA989D9B62 ON gas_type');
        $this->addSql('ALTER TABLE gas_type DROP name, DROP slug');
    }
}
