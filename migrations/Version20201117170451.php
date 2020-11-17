<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117170451 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE BENEFICIAIRES');
        $this->addSql('ALTER TABLE user ADD google_id VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64976F5C865 ON user (google_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE BENEFICIAIRES (id INT AUTO_INCREMENT NOT NULL, civilite INT DEFAULT NULL, matricule VARCHAR(32) CHARACTER SET utf8 DEFAULT \'\' NOT NULL COLLATE `utf8_general_ci`, nom VARCHAR(128) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, prenom VARCHAR(128) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, nom_usage VARCHAR(128) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, prenom_usage VARCHAR(128) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, nom_jeune_fille VARCHAR(128) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, sexe INT NOT NULL, nationalite VARCHAR(30) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, password VARCHAR(32) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, date_naissance DATE DEFAULT NULL, date_entree DATE DEFAULT NULL, date_sortie DATE DEFAULT NULL, motif_sortie VARCHAR(150) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, date_debut_droits DATE DEFAULT NULL, date_fin_droits DATE DEFAULT NULL, id_statut_marital INT DEFAULT NULL, date_deces DATE DEFAULT NULL, handicap TINYINT(1) DEFAULT NULL, date_handicap DATE DEFAULT NULL, contre_indication TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, contact_urgence_identique TINYINT(1) DEFAULT NULL COMMENT \'Si les contacts en cas d\'\'urgence sont communs à toute la famille\', complement_adresse VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, numero_adresse VARCHAR(10) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, code_postal VARCHAR(5) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ville CHAR(50) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, telephone_portable VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, telephone_fixe_perso VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, badge_nedap VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, id_comite_entreprise INT NOT NULL, ce_par_defaut INT DEFAULT NULL, id_nature INT NOT NULL, statut_contrat VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, id_site INT DEFAULT NULL, ipn VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, numeros_badges VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, mains_libres VARCHAR(15) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, mifare VARCHAR(30) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, numero_service VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, adresse_renault VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, id_csp INT NOT NULL, badge_stid VARCHAR(200) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, telephone_fixe_pro VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, telephone_portable_pro VARCHAR(20) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, remarque TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, informations_bancaires INT NOT NULL, date_suppression DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' COMMENT \'Field for active people. will be null if deleted by proxemy\', taux NUMERIC(8, 2) DEFAULT NULL, compte_bloque TINYINT(1) DEFAULT \'0\' NOT NULL, processus_courant INT DEFAULT NULL, date_derogation DATETIME DEFAULT NULL, id_derogation INT DEFAULT NULL, nom_phonetique VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, prenom_phonetique VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, logover INT DEFAULT 0, ticket_password VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, logover_email VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, handicap80 TINYINT(1) DEFAULT \'0\' NOT NULL, image VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, matricule_delegation VARCHAR(32) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, is_admin TINYINT(1) DEFAULT \'0\' NOT NULL, notify_channels VARCHAR(200) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci` COMMENT \'comma separated Notify\\\\Channel names. see \\\\LogiCE\\\\Module\\\\Notifier\\\\Channel\', third_parties_account VARCHAR(250) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, date_import DATETIME DEFAULT NULL, analytic_section VARCHAR(50) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, id_analytic_code VARCHAR(200) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, date_arrival DATE DEFAULT NULL, INDEX benef_import (nom(20), prenom(10), date_naissance), INDEX nature (id_nature), FULLTEXT INDEX beneficiaire_search (nom, prenom, matricule, nom_usage, prenom_usage, nom_jeune_fille, nom_phonetique, prenom_phonetique), FULLTEXT INDEX nationalite (nationalite), INDEX matricule_delegation (matricule_delegation), UNIQUE INDEX matricule (matricule), INDEX logover (logover), INDEX id_statut_marital (id_statut_marital), INDEX id_site_beneficiaires (id_site), INDEX id_derogation (id_derogation), FULLTEXT INDEX beneficiaire_search_badge (mains_libres, mifare), INDEX id_csp (id_csp), FULLTEXT INDEX beneficiaire_search_basic (matricule, nom, prenom), INDEX id_comite_entreprise (id_comite_entreprise), INDEX id_benef (id), FULLTEXT INDEX beneficiaire_search_basic_without_matricule (nom, prenom), INDEX date_suppression (date_suppression), FULLTEXT INDEX beneficiaire_search_phonetique (nom_phonetique, prenom_phonetique), INDEX civilite_beneficiaires (civilite), FULLTEXT INDEX beneficiaire_search_usage (nom_usage, prenom_usage), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'unique Matricule\' ');
        $this->addSql('DROP INDEX UNIQ_8D93D64976F5C865 ON user');
        $this->addSql('ALTER TABLE user DROP google_id');
    }
}
