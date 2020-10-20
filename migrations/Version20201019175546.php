<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019175546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station_services RENAME INDEX idx_fd56a06bed5ca9e6 TO IDX_C193217BED5CA9E6');
        $this->addSql('ALTER TABLE gas_station_services RENAME INDEX idx_fd56a06b21bdb235 TO IDX_C193217B21BDB235');
        $this->addSql('ALTER TABLE gas_station CHANGE last_prices last_prices LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station CHANGE last_prices last_prices JSON NOT NULL');
        $this->addSql('ALTER TABLE gas_station_services RENAME INDEX idx_c193217b21bdb235 TO IDX_FD56A06B21BDB235');
        $this->addSql('ALTER TABLE gas_station_services RENAME INDEX idx_c193217bed5ca9e6 TO IDX_FD56A06BED5CA9E6');
    }
}
