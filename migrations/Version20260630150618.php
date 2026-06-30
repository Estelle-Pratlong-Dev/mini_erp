<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630150618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE taux_tva (id INT AUTO_INCREMENT NOT NULL, taux NUMERIC(5, 2) NOT NULL, libelle VARCHAR(50) DEFAULT NULL, actif TINYINT NOT NULL, supprime TINYINT DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_71F260E8FC29C013 (cree_par_id), INDEX IDX_71F260E8553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE taux_tva ADD CONSTRAINT FK_71F260E8FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE taux_tva ADD CONSTRAINT FK_71F260E8553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE taux_tva DROP FOREIGN KEY FK_71F260E8FC29C013');
        $this->addSql('ALTER TABLE taux_tva DROP FOREIGN KEY FK_71F260E8553B2554');
        $this->addSql('DROP TABLE taux_tva');
    }
}
