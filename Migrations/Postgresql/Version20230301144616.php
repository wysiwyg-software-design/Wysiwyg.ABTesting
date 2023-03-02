<?php

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230301144616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for A/B Testing';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on 'PostgreSQL'."
        );

        $this->addSql('CREATE TABLE wysiwyg_abtesting_domain_model_decision (persistence_object_identifier VARCHAR(40) NOT NULL, feature VARCHAR(40) DEFAULT NULL, deciderclassname VARCHAR(255) NOT NULL, decision JSON NOT NULL, defaultdecision VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier))');
        $this->addSql('CREATE INDEX IDX_8E96F03A1FD77566 ON wysiwyg_abtesting_domain_model_decision (feature)');
        $this->addSql('COMMENT ON COLUMN wysiwyg_abtesting_domain_model_decision.decision IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE wysiwyg_abtesting_domain_model_feature (persistence_object_identifier VARCHAR(40) NOT NULL, featurename VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(persistence_object_identifier))');
        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision ADD CONSTRAINT FK_8E96F03A1FD77566 FOREIGN KEY (feature) REFERENCES wysiwyg_abtesting_domain_model_feature (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on 'PostgreSQL'."
        );

        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision DROP CONSTRAINT FK_8E96F03A1FD77566');
        $this->addSql('DROP TABLE wysiwyg_abtesting_domain_model_decision');
        $this->addSql('DROP TABLE wysiwyg_abtesting_domain_model_feature');
    }
}
