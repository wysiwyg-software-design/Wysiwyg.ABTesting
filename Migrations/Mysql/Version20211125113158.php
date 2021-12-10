<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20211125113158 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'add "defaultdecision" to decision table';
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

        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision ADD defaultdecision VARCHAR(255) NOT NULL');
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

        $this->addSql('ALTER TABLE wysiwyg_abtesting_domain_model_decision DROP defaultdecision');
    }
}
