<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622172149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD supprime TINYINT DEFAULT 0 NOT NULL, DROP supprime_le');
        $this->addSql('ALTER TABLE contrat ADD supprime TINYINT DEFAULT 0 NOT NULL, DROP supprime_le');
        $this->addSql('ALTER TABLE ligne_document ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_document ADD CONSTRAINT FK_87F39B40FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ligne_document ADD CONSTRAINT FK_87F39B40553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_87F39B40FC29C013 ON ligne_document (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_87F39B40553B2554 ON ligne_document (modifie_par_id)');
        $this->addSql('ALTER TABLE module ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C242628FC29C013 ON module (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_C242628553B2554 ON module (modifie_par_id)');
        $this->addSql('ALTER TABLE permission ADD supprime TINYINT DEFAULT 0 NOT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, CHANGE supprime_le cree_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAFC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E04992AAFC29C013 ON permission (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_E04992AA553B2554 ON permission (modifie_par_id)');
        $this->addSql('ALTER TABLE piece_jointe ADD supprime TINYINT DEFAULT 0 NOT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, DROP date_ajout, CHANGE supprime_le cree_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D4FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D4553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AB5111D4FC29C013 ON piece_jointe (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_AB5111D4553B2554 ON piece_jointe (modifie_par_id)');
        $this->addSql('ALTER TABLE produit ADD supprime TINYINT DEFAULT 0 NOT NULL, DROP supprime_le');
        $this->addSql('ALTER TABLE projet ADD supprime TINYINT DEFAULT 0 NOT NULL, DROP supprime_le');
        $this->addSql('ALTER TABLE role ADD supprime TINYINT DEFAULT 0 NOT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, CHANGE supprime_le cree_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AFC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_57698A6AFC29C013 ON role (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_57698A6A553B2554 ON role (modifie_par_id)');
        $this->addSql('ALTER TABLE societe ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBDFC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBD553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_19653DBDFC29C013 ON societe (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_19653DBD553B2554 ON societe (modifie_par_id)');
        $this->addSql('ALTER TABLE user ADD supprime TINYINT DEFAULT 0 NOT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, CHANGE supprime_le cree_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649FC29C013 ON user (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649553B2554 ON user (modifie_par_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD supprime_le DATETIME DEFAULT NULL, DROP supprime');
        $this->addSql('ALTER TABLE contrat ADD supprime_le DATETIME DEFAULT NULL, DROP supprime');
        $this->addSql('ALTER TABLE ligne_document DROP FOREIGN KEY FK_87F39B40FC29C013');
        $this->addSql('ALTER TABLE ligne_document DROP FOREIGN KEY FK_87F39B40553B2554');
        $this->addSql('DROP INDEX IDX_87F39B40FC29C013 ON ligne_document');
        $this->addSql('DROP INDEX IDX_87F39B40553B2554 ON ligne_document');
        $this->addSql('ALTER TABLE ligne_document DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628FC29C013');
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628553B2554');
        $this->addSql('DROP INDEX IDX_C242628FC29C013 ON module');
        $this->addSql('DROP INDEX IDX_C242628553B2554 ON module');
        $this->addSql('ALTER TABLE module DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAFC29C013');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA553B2554');
        $this->addSql('DROP INDEX IDX_E04992AAFC29C013 ON permission');
        $this->addSql('DROP INDEX IDX_E04992AA553B2554 ON permission');
        $this->addSql('ALTER TABLE permission ADD supprime_le DATETIME DEFAULT NULL, DROP supprime, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE piece_jointe DROP FOREIGN KEY FK_AB5111D4FC29C013');
        $this->addSql('ALTER TABLE piece_jointe DROP FOREIGN KEY FK_AB5111D4553B2554');
        $this->addSql('DROP INDEX IDX_AB5111D4FC29C013 ON piece_jointe');
        $this->addSql('DROP INDEX IDX_AB5111D4553B2554 ON piece_jointe');
        $this->addSql('ALTER TABLE piece_jointe ADD date_ajout DATETIME NOT NULL, ADD supprime_le DATETIME DEFAULT NULL, DROP supprime, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE produit ADD supprime_le DATETIME DEFAULT NULL, DROP supprime');
        $this->addSql('ALTER TABLE projet ADD supprime_le DATETIME DEFAULT NULL, DROP supprime');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AFC29C013');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A553B2554');
        $this->addSql('DROP INDEX IDX_57698A6AFC29C013 ON role');
        $this->addSql('DROP INDEX IDX_57698A6A553B2554 ON role');
        $this->addSql('ALTER TABLE role ADD supprime_le DATETIME DEFAULT NULL, DROP supprime, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBDFC29C013');
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBD553B2554');
        $this->addSql('DROP INDEX IDX_19653DBDFC29C013 ON societe');
        $this->addSql('DROP INDEX IDX_19653DBD553B2554 ON societe');
        $this->addSql('ALTER TABLE societe DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FC29C013');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649553B2554');
        $this->addSql('DROP INDEX IDX_8D93D649FC29C013 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649553B2554 ON user');
        $this->addSql('ALTER TABLE user ADD supprime_le DATETIME DEFAULT NULL, DROP supprime, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
    }
}
