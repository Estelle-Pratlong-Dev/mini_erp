<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622170402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE contrat ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE permission ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE piece_jointe ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE projet ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD supprime_le DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD supprime_le DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP supprime_le');
        $this->addSql('ALTER TABLE contrat DROP supprime_le');
        $this->addSql('ALTER TABLE permission DROP supprime_le');
        $this->addSql('ALTER TABLE piece_jointe DROP supprime_le');
        $this->addSql('ALTER TABLE produit DROP supprime_le');
        $this->addSql('ALTER TABLE projet DROP supprime_le');
        $this->addSql('ALTER TABLE role DROP supprime_le');
        $this->addSql('ALTER TABLE user DROP supprime_le');
    }
}
