<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622191638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_87f39b401823061f TO IDX_9DA3305F1823061F');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_87f39b40f347efb TO IDX_9DA3305FF347EFB');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_87f39b40fc29c013 TO IDX_9DA3305FFC29C013');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_87f39b40553b2554 TO IDX_9DA3305F553B2554');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_9da3305f1823061f TO IDX_87F39B401823061F');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_9da3305f553b2554 TO IDX_87F39B40553B2554');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_9da3305ff347efb TO IDX_87F39B40F347EFB');
        $this->addSql('ALTER TABLE ligne_article RENAME INDEX idx_9da3305ffc29c013 TO IDX_87F39B40FC29C013');
    }
}
