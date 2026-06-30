<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630133613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delai_paiement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, jours INT NOT NULL, actif TINYINT NOT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_BFF37CA3FC29C013 (cree_par_id), INDEX IDX_BFF37CA3553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mode_paiement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, actif TINYINT NOT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_B2BB0E85FC29C013 (cree_par_id), INDEX IDX_B2BB0E85553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE delai_paiement ADD CONSTRAINT FK_BFF37CA3FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE delai_paiement ADD CONSTRAINT FK_BFF37CA3553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mode_paiement ADD CONSTRAINT FK_B2BB0E85553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture ADD delai_paiement_id INT DEFAULT NULL, ADD mode_paiement_id INT DEFAULT NULL, DROP delai_paiement, DROP mode_paiement');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664101A41B3EA FOREIGN KEY (delai_paiement_id) REFERENCES delai_paiement (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410438F5B63 FOREIGN KEY (mode_paiement_id) REFERENCES mode_paiement (id)');
        $this->addSql('CREATE INDEX IDX_FE8664101A41B3EA ON facture (delai_paiement_id)');
        $this->addSql('CREATE INDEX IDX_FE866410438F5B63 ON facture (mode_paiement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delai_paiement DROP FOREIGN KEY FK_BFF37CA3FC29C013');
        $this->addSql('ALTER TABLE delai_paiement DROP FOREIGN KEY FK_BFF37CA3553B2554');
        $this->addSql('ALTER TABLE mode_paiement DROP FOREIGN KEY FK_B2BB0E85FC29C013');
        $this->addSql('ALTER TABLE mode_paiement DROP FOREIGN KEY FK_B2BB0E85553B2554');
        $this->addSql('DROP TABLE delai_paiement');
        $this->addSql('DROP TABLE mode_paiement');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664101A41B3EA');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410438F5B63');
        $this->addSql('DROP INDEX IDX_FE8664101A41B3EA ON facture');
        $this->addSql('DROP INDEX IDX_FE866410438F5B63 ON facture');
        $this->addSql('ALTER TABLE facture ADD delai_paiement VARCHAR(20) DEFAULT NULL, ADD mode_paiement VARCHAR(20) DEFAULT NULL, DROP delai_paiement_id, DROP mode_paiement_id');
    }
}
