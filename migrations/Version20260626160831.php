<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626160831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE composant (id INT AUTO_INCREMENT NOT NULL, quantite NUMERIC(12, 3) NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, produit_parent_id INT NOT NULL, composant_id INT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, INDEX IDX_EC8486C9F6ED949C (produit_parent_id), INDEX IDX_EC8486C97F3310E7 (composant_id), INDEX IDX_EC8486C9FC29C013 (cree_par_id), INDEX IDX_EC8486C9553B2554 (modifie_par_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE composant ADD CONSTRAINT FK_EC8486C9F6ED949C FOREIGN KEY (produit_parent_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE composant ADD CONSTRAINT FK_EC8486C97F3310E7 FOREIGN KEY (composant_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE composant ADD CONSTRAINT FK_EC8486C9FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE composant ADD CONSTRAINT FK_EC8486C9553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE composant DROP FOREIGN KEY FK_EC8486C9F6ED949C');
        $this->addSql('ALTER TABLE composant DROP FOREIGN KEY FK_EC8486C97F3310E7');
        $this->addSql('ALTER TABLE composant DROP FOREIGN KEY FK_EC8486C9FC29C013');
        $this->addSql('ALTER TABLE composant DROP FOREIGN KEY FK_EC8486C9553B2554');
        $this->addSql('DROP TABLE composant');
    }
}
