<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016174404 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_token (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, user_id VARCHAR(255) NOT NULL, jwt_hash LONGTEXT NOT NULL, expire_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9315F04EDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_price (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EEF8FDB6DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_service (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_159406CFDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_station (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6B3064ACDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gas_type (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8A29EDADE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_place (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, place_id VARCHAR(255) DEFAULT NULL, compound_code VARCHAR(255) DEFAULT NULL, global_code VARCHAR(255) DEFAULT NULL, google_rating VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, user_ratings_total VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, business_status VARCHAR(255) DEFAULT NULL, nearbysearch LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EDF05AC2DA6A219 (place_id), INDEX IDX_EDF05AC2DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_token ADD CONSTRAINT FK_9315F04EDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_price ADD CONSTRAINT FK_EEF8FDB6DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_service ADD CONSTRAINT FK_159406CFDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gas_type ADD CONSTRAINT FK_8A29EDADE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE google_place ADD CONSTRAINT FK_EDF05AC2DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_token DROP FOREIGN KEY FK_9315F04EDE12AB56');
        $this->addSql('ALTER TABLE gas_price DROP FOREIGN KEY FK_EEF8FDB6DE12AB56');
        $this->addSql('ALTER TABLE gas_service DROP FOREIGN KEY FK_159406CFDE12AB56');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACDE12AB56');
        $this->addSql('ALTER TABLE gas_type DROP FOREIGN KEY FK_8A29EDADE12AB56');
        $this->addSql('ALTER TABLE google_place DROP FOREIGN KEY FK_EDF05AC2DE12AB56');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DE12AB56');
        $this->addSql('DROP TABLE auth_token');
        $this->addSql('DROP TABLE gas_price');
        $this->addSql('DROP TABLE gas_service');
        $this->addSql('DROP TABLE gas_station');
        $this->addSql('DROP TABLE gas_type');
        $this->addSql('DROP TABLE google_place');
        $this->addSql('DROP TABLE user');
    }
}
