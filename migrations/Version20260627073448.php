<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260627073448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, actif TINYINT NOT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_13E1489AFC29C013 (cree_par_id), INDEX IDX_13E1489A553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE categorie_contact ADD CONSTRAINT FK_13E1489AFC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE categorie_contact ADD CONSTRAINT FK_13E1489A553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_contact (id)');
        $this->addSql('CREATE INDEX IDX_4C62E638BCF5E72D ON contact (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_contact DROP FOREIGN KEY FK_13E1489AFC29C013');
        $this->addSql('ALTER TABLE categorie_contact DROP FOREIGN KEY FK_13E1489A553B2554');
        $this->addSql('DROP TABLE categorie_contact');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638BCF5E72D');
        $this->addSql('DROP INDEX IDX_4C62E638BCF5E72D ON contact');
        $this->addSql('ALTER TABLE contact DROP categorie_id');
    }
}
