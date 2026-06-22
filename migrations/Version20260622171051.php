<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622171051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY `FK_4C62E638896DBBDE`');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY `FK_4C62E638B03A8386`');
        $this->addSql('DROP INDEX IDX_4C62E638896DBBDE ON contact');
        $this->addSql('DROP INDEX IDX_4C62E638B03A8386 ON contact');
        $this->addSql('ALTER TABLE contact ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, DROP created_at, DROP updated_at, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4C62E638FC29C013 ON contact (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_4C62E638553B2554 ON contact (modifie_par_id)');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY `FK_60349993896DBBDE`');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY `FK_60349993B03A8386`');
        $this->addSql('DROP INDEX IDX_60349993896DBBDE ON contrat');
        $this->addSql('DROP INDEX IDX_60349993B03A8386 ON contrat');
        $this->addSql('ALTER TABLE contrat ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, DROP created_at, DROP updated_at, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_60349993FC29C013 ON contrat (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_60349993553B2554 ON contrat (modifie_par_id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY `FK_29A5EC27896DBBDE`');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY `FK_29A5EC27B03A8386`');
        $this->addSql('DROP INDEX IDX_29A5EC27896DBBDE ON produit');
        $this->addSql('DROP INDEX IDX_29A5EC27B03A8386 ON produit');
        $this->addSql('ALTER TABLE produit ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, DROP created_at, DROP updated_at, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27FC29C013 ON produit (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27553B2554 ON produit (modifie_par_id)');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY `FK_50159CA9896DBBDE`');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY `FK_50159CA9B03A8386`');
        $this->addSql('DROP INDEX IDX_50159CA9896DBBDE ON projet');
        $this->addSql('DROP INDEX IDX_50159CA9B03A8386 ON projet');
        $this->addSql('ALTER TABLE projet ADD cree_le DATETIME DEFAULT NULL, ADD modifie_le DATETIME DEFAULT NULL, ADD cree_par_id INT DEFAULT NULL, ADD modifie_par_id INT DEFAULT NULL, DROP created_at, DROP updated_at, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9FC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50159CA9FC29C013 ON projet (cree_par_id)');
        $this->addSql('CREATE INDEX IDX_50159CA9553B2554 ON projet (modifie_par_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638FC29C013');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638553B2554');
        $this->addSql('DROP INDEX IDX_4C62E638FC29C013 ON contact');
        $this->addSql('DROP INDEX IDX_4C62E638553B2554 ON contact');
        $this->addSql('ALTER TABLE contact ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT `FK_4C62E638896DBBDE` FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT `FK_4C62E638B03A8386` FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4C62E638896DBBDE ON contact (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_4C62E638B03A8386 ON contact (created_by_id)');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993FC29C013');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993553B2554');
        $this->addSql('DROP INDEX IDX_60349993FC29C013 ON contrat');
        $this->addSql('DROP INDEX IDX_60349993553B2554 ON contrat');
        $this->addSql('ALTER TABLE contrat ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT `FK_60349993896DBBDE` FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT `FK_60349993B03A8386` FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_60349993896DBBDE ON contrat (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_60349993B03A8386 ON contrat (created_by_id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27FC29C013');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27553B2554');
        $this->addSql('DROP INDEX IDX_29A5EC27FC29C013 ON produit');
        $this->addSql('DROP INDEX IDX_29A5EC27553B2554 ON produit');
        $this->addSql('ALTER TABLE produit ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT `FK_29A5EC27896DBBDE` FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT `FK_29A5EC27B03A8386` FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_29A5EC27896DBBDE ON produit (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27B03A8386 ON produit (created_by_id)');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9FC29C013');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9553B2554');
        $this->addSql('DROP INDEX IDX_50159CA9FC29C013 ON projet');
        $this->addSql('DROP INDEX IDX_50159CA9553B2554 ON projet');
        $this->addSql('ALTER TABLE projet ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP cree_le, DROP modifie_le, DROP cree_par_id, DROP modifie_par_id');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT `FK_50159CA9896DBBDE` FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT `FK_50159CA9B03A8386` FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_50159CA9896DBBDE ON projet (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_50159CA9B03A8386 ON projet (created_by_id)');
    }
}
