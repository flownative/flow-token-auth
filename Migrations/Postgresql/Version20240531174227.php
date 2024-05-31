<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531174227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE flownative_tokenauthentication_security_model_hashandroles (hash VARCHAR(255) NOT NULL, roleshash VARCHAR(255) NOT NULL, roles JSON NOT NULL, settings JSON NOT NULL, PRIMARY KEY(hash))');
        $this->addSql('COMMENT ON COLUMN flownative_tokenauthentication_security_model_hashandroles.roles IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN flownative_tokenauthentication_security_model_hashandroles.settings IS \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE flownative_tokenauthentication_security_model_hashandroles');
    }
}
