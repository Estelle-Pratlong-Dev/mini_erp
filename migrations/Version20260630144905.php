<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260630144905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat ADD contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_60349993E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('CREATE INDEX IDX_60349993E7A1254A ON contrat (contact_id)');
        $this->addSql('ALTER TABLE societe ADD prefixe_contrat VARCHAR(20) DEFAULT \'DEV-\' NOT NULL, ADD prochain_numero_contrat INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_60349993E7A1254A');
        $this->addSql('DROP INDEX IDX_60349993E7A1254A ON contrat');
        $this->addSql('ALTER TABLE contrat DROP contact_id');
        $this->addSql('ALTER TABLE societe DROP prefixe_contrat, DROP prochain_numero_contrat');
    }
}
