<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025142409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station ADD preview_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACCDE46FDB FOREIGN KEY (preview_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B3064ACCDE46FDB ON gas_station (preview_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACCDE46FDB');
        $this->addSql('DROP INDEX UNIQ_6B3064ACCDE46FDB ON gas_station');
        $this->addSql('ALTER TABLE gas_station DROP preview_id');
    }
}
