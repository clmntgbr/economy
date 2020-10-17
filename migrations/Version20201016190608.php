<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016190608 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station ADD google_place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064AC983C031 FOREIGN KEY (google_place_id) REFERENCES google_place (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B3064AC983C031 ON gas_station (google_place_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064AC983C031');
        $this->addSql('DROP INDEX UNIQ_6B3064AC983C031 ON gas_station');
        $this->addSql('ALTER TABLE gas_station DROP google_place_id');
    }
}
