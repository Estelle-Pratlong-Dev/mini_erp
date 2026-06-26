<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626150143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture ADD delai_paiement INT DEFAULT NULL, ADD mode_paiement VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_article ADD ligne_source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_article ADD CONSTRAINT FK_9DA3305F5AD913E9 FOREIGN KEY (ligne_source_id) REFERENCES ligne_article (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9DA3305F5AD913E9 ON ligne_article (ligne_source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP delai_paiement, DROP mode_paiement');
        $this->addSql('ALTER TABLE ligne_article DROP FOREIGN KEY FK_9DA3305F5AD913E9');
        $this->addSql('DROP INDEX IDX_9DA3305F5AD913E9 ON ligne_article');
        $this->addSql('ALTER TABLE ligne_article DROP ligne_source_id');
    }
}
