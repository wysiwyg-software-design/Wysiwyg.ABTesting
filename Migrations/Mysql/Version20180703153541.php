<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180703153541 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create tables for A/B Testing';
    }

    /**
     * @param Schema $schema
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('CREATE TABLE wysiwyg_abtesting_domain_model_decision (persistence_object_identifier VARCHAR(40) NOT NULL, feature VARCHAR(40) DEFAULT NULL, decider VARCHAR(255) NOT NULL, decision LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', defaultdecision VARCHAR(255) NOT NULL, priority INT NOT NULL, INDEX IDX_8E96F03A1FD77566 (feature), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wysiwyg_abtesting_domain_model_feature (persistence_object_identifier VARCHAR(40) NOT NULL, featurename VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision ADD CONSTRAINT FK_8E96F03A1FD77566 FOREIGN KEY (feature) REFERENCES wysiwyg_abtesting_domain_model_feature (persistence_object_identifier)');
    }

    /**
     * @param Schema $schema
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision DROP FOREIGN KEY FK_8E96F03A1FD77566');
        $this->addSql('DROP TABLE wysiwyg_abtesting_domain_model_decision');
        $this->addSql('DROP TABLE wysiwyg_abtesting_domain_model_feature');
    }
}
