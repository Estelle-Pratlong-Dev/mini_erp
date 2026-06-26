<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626113634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_PERMISSION_CODE ON permission');
        $this->addSql('DROP INDEX UNIQ_PRODUIT_REFERENCE ON produit');
        $this->addSql('DROP INDEX UNIQ_ROLE_CODE ON role');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PERMISSION_CODE ON permission (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PRODUIT_REFERENCE ON produit (reference)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ROLE_CODE ON role (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }
}
