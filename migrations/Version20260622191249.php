<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622191249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renomme la table ligne_document en ligne_article (données conservées).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE ligne_document TO ligne_article');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE ligne_article TO ligne_document');
    }
}
