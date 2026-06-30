<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630135912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE unite_produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, actif TINYINT NOT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_93CCAD84FC29C013 (cree_par_id), INDEX IDX_93CCAD84553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE unite_produit ADD CONSTRAINT FK_93CCAD84FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE unite_produit ADD CONSTRAINT FK_93CCAD84553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD prix_achat_ht NUMERIC(12, 2) NOT NULL, ADD unite_id INT DEFAULT NULL, DROP unite');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite_produit (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27EC4A74AB ON produit (unite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unite_produit DROP FOREIGN KEY FK_93CCAD84FC29C013');
        $this->addSql('ALTER TABLE unite_produit DROP FOREIGN KEY FK_93CCAD84553B2554');
        $this->addSql('DROP TABLE unite_produit');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27EC4A74AB');
        $this->addSql('DROP INDEX IDX_29A5EC27EC4A74AB ON produit');
        $this->addSql('ALTER TABLE produit ADD unite VARCHAR(20) DEFAULT NULL, DROP prix_achat_ht, DROP unite_id');
    }
}
