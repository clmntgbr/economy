<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025142006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gas_station_reviews (station_id INT NOT NULL, review_id INT NOT NULL, INDEX IDX_75BBD1B621BDB235 (station_id), INDEX IDX_75BBD1B63E2E969B (review_id), PRIMARY KEY(station_id, review_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, text LONGTEXT DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, rating VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, user VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_794381C6DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gas_station_reviews ADD CONSTRAINT FK_75BBD1B621BDB235 FOREIGN KEY (station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station_reviews ADD CONSTRAINT FK_75BBD1B63E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station_reviews DROP FOREIGN KEY FK_75BBD1B63E2E969B');
        $this->addSql('DROP TABLE gas_station_reviews');
        $this->addSql('DROP TABLE review');
    }
}
