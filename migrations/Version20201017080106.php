<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017080106 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, vicinity VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, number VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, region VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, longitude VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_token (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, user_id VARCHAR(255) NOT NULL, jwt_hash LONGTEXT NOT NULL, expire_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9315F04EDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_price (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, station_id INT DEFAULT NULL, created_by INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, date_timestamp INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EEF8FDB6C54C8C93 (type_id), INDEX IDX_EEF8FDB621BDB235 (station_id), INDEX IDX_EEF8FDB6DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_service (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_159406CFDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station_services (service_id INT NOT NULL, station_id INT NOT NULL, INDEX IDX_FD56A06BED5CA9E6 (service_id), INDEX IDX_FD56A06B21BDB235 (station_id), PRIMARY KEY(service_id, station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station (id INT NOT NULL, address_id INT DEFAULT NULL, google_place_id INT DEFAULT NULL, created_by INT DEFAULT NULL, pop VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, element LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_closed TINYINT(1) NOT NULL, closed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6B3064ACF5B7AF75 (address_id), UNIQUE INDEX UNIQ_6B3064AC983C031 (google_place_id), INDEX IDX_6B3064ACDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_type (id INT NOT NULL, created_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8A29EDA989D9B62 (slug), INDEX IDX_8A29EDADE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_place (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, place_id VARCHAR(255) DEFAULT NULL, compound_code VARCHAR(255) DEFAULT NULL, global_code VARCHAR(255) DEFAULT NULL, google_rating VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, user_ratings_total VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, business_status VARCHAR(255) DEFAULT NULL, nearbysearch LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EDF05AC2DA6A219 (place_id), INDEX IDX_EDF05AC2DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB6C54C8C93 FOREIGN KEY (type_id) REFERENCES gas_type (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB621BDB235 FOREIGN KEY (station_id) REFERENCES gas_station (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_service ADD CONSTRAINT FK_159406CFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_station_services ADD CONSTRAINT FK_FD56A06BED5CA9E6 FOREIGN KEY (service_id) REFERENCES gas_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station_services ADD CONSTRAINT FK_FD56A06B21BDB235 FOREIGN KEY (station_id) REFERENCES gas_station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064AC983C031 FOREIGN KEY (google_place_id) REFERENCES google_place (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_type ADD CONSTRAINT FK_8A29EDADE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE google_place ADD CONSTRAINT FK_EDF05AC2DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACF5B7AF75');
        $this->addSql('ALTER TABLE gas_station_services DROP FOREIGN KEY FK_FD56A06BED5CA9E6');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB621BDB235');
        $this->addSql('ALTER TABLE gas_station_services DROP FOREIGN KEY FK_FD56A06B21BDB235');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB6C54C8C93');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064AC983C031');
        $this->addSql('ALTER TABLE auth_token DROP FOREIGN KEY FK_9315F04EDE12AB56');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB6DE12AB56');
        $this->addSql('ALTER TABLE gas_service DROP FOREIGN KEY FK_159406CFDE12AB56');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACDE12AB56');
        $this->addSql('ALTER TABLE gas_type DROP FOREIGN KEY FK_8A29EDADE12AB56');
        $this->addSql('ALTER TABLE google_place DROP FOREIGN KEY FK_EDF05AC2DE12AB56');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DE12AB56');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE auth_token');
        $this->addSql('DROP TABLE gas_price');
        $this->addSql('DROP TABLE gas_service');
        $this->addSql('DROP TABLE gas_station_services');
        $this->addSql('DROP TABLE gas_station');
        $this->addSql('DROP TABLE gas_type');
        $this->addSql('DROP TABLE google_place');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
