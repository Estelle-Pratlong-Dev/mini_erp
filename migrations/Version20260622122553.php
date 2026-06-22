<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622122553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) DEFAULT NULL, type VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, date_emission DATE NOT NULL, date_validite DATE DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, projet_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_60349993C18272 (projet_id), INDEX IDX_60349993B03A8386 (created_by_id), INDEX IDX_60349993896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_document (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, quantite NUMERIC(12, 3) NOT NULL, prix_unitaire_ht NUMERIC(12, 2) NOT NULL, taux_tva NUMERIC(5, 2) NOT NULL, contrat_id INT DEFAULT NULL, produit_id INT DEFAULT NULL, INDEX IDX_87F39B401823061F (contrat_id), INDEX IDX_87F39B40F347EFB (produit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE piece_jointe (id INT AUTO_INCREMENT NOT NULL, fichier VARCHAR(255) NOT NULL, nom_original VARCHAR(255) NOT NULL, type_mime VARCHAR(150) DEFAULT NULL, taille INT DEFAULT NULL, date_ajout DATETIME NOT NULL, projet_id INT DEFAULT NULL, contrat_id INT DEFAULT NULL, INDEX IDX_AB5111D4C18272 (projet_id), INDEX IDX_AB5111D41823061F (contrat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date DATE NOT NULL, statut VARCHAR(20) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, contact_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_50159CA9E7A1254A (contact_id), INDEX IDX_50159CA9B03A8386 (created_by_id), INDEX IDX_50159CA9896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ligne_document ADD CONSTRAINT FK_87F39B401823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE ligne_document ADD CONSTRAINT FK_87F39B40F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D4C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE piece_jointe ADD CONSTRAINT FK_AB5111D41823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993C18272');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993B03A8386');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993896DBBDE');
        $this->addSql('ALTER TABLE ligne_document DROP FOREIGN KEY FK_87F39B401823061F');
        $this->addSql('ALTER TABLE ligne_document DROP FOREIGN KEY FK_87F39B40F347EFB');
        $this->addSql('ALTER TABLE piece_jointe DROP FOREIGN KEY FK_AB5111D4C18272');
        $this->addSql('ALTER TABLE piece_jointe DROP FOREIGN KEY FK_AB5111D41823061F');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9E7A1254A');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9B03A8386');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9896DBBDE');
        $this->addSql('DROP TABLE contrat');
        $this->addSql('DROP TABLE ligne_document');
        $this->addSql('DROP TABLE piece_jointe');
        $this->addSql('DROP TABLE projet');
    }
}
