<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626115803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(50) DEFAULT NULL, statut VARCHAR(20) NOT NULL, date_emission DATE NOT NULL, date_echeance DATE DEFAULT NULL, notes LONGTEXT DEFAULT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, projet_id INT NOT NULL, contact_id INT DEFAULT NULL, contrat_id INT DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_FE866410C18272 (projet_id), INDEX IDX_FE866410E7A1254A (contact_id), INDEX IDX_FE8664101823061F (contrat_id), INDEX IDX_FE866410FC29C013 (cree_par_id), INDEX IDX_FE866410553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664101823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ligne_article ADD facture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_article ADD CONSTRAINT FK_9DA3305F7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE INDEX IDX_9DA3305F7F2DEE08 ON ligne_article (facture_id)');
        $this->addSql('ALTER TABLE piece_jointe ADD facture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D47F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE INDEX IDX_AB5111D47F2DEE08 ON piece_jointe (facture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410C18272');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410E7A1254A');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664101823061F');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410FC29C013');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410553B2554');
        $this->addSql('DROP TABLE facture');
        $this->addSql('ALTER TABLE ligne_article DROP FOREIGN KEY FK_9DA3305F7F2DEE08');
        $this->addSql('DROP INDEX IDX_9DA3305F7F2DEE08 ON ligne_article');
        $this->addSql('ALTER TABLE ligne_article DROP facture_id');
        $this->addSql('ALTER TABLE piece_jointe DROP FOREIGN KEY FK_AB5111D47F2DEE08');
        $this->addSql('DROP INDEX IDX_AB5111D47F2DEE08 ON piece_jointe');
        $this->addSql('ALTER TABLE piece_jointe DROP facture_id');
    }
}
