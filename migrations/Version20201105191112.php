<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105191112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review ADD author_name VARCHAR(255) DEFAULT NULL, ADD author_url VARCHAR(255) DEFAULT NULL, ADD profile_photo_url VARCHAR(255) DEFAULT NULL, ADD relative_time_description VARCHAR(255) DEFAULT NULL, ADD date_timestamp INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP author_name, DROP author_url, DROP profile_photo_url, DROP relative_time_description, DROP date_timestamp');
    }
}
