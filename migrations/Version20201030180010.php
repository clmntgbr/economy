<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201030180010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_like_gas_stations RENAME INDEX idx_44c96caea76ed395 TO IDX_36A9C04EA76ED395');
        $this->addSql('ALTER TABLE user_like_gas_stations RENAME INDEX idx_44c96cae21bdb235 TO IDX_36A9C04E21BDB235');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_like_gas_stations RENAME INDEX idx_36a9c04e21bdb235 TO IDX_44C96CAE21BDB235');
        $this->addSql('ALTER TABLE user_like_gas_stations RENAME INDEX idx_36a9c04ea76ed395 TO IDX_44C96CAEA76ED395');
    }
}
