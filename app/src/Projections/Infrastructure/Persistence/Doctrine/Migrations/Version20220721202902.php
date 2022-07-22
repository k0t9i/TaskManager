<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220721202902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_projections (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, status INT NOT NULL, change_date TIMESTAMPTZ NOT NULL, user_id VARCHAR(36) NOT NULL, user_firstname VARCHAR(255) NOT NULL, user_lastname VARCHAR(255) NOT NULL, user_email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E27FED6B166D1F9C ON request_projections (project_id)');
        $this->addSql('CREATE INDEX IDX_E27FED6BA76ED395 ON request_projections (user_id)');

        $this->addSql('
            CREATE VIEW v_request_projections AS
                SELECT t.*, p.owner_id project_owner_id FROM request_projections t
                LEFT JOIN (select distinct owner_id, id from project_projections) p ON t.project_id = p.id
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW v_request_projections');
        $this->addSql('DROP TABLE request_projections');
    }
}
