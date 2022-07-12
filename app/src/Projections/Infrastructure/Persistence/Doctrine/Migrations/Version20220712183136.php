<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220712183136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project_projections (id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, finish_date DATE NOT NULL, status INT NOT NULL, owner_id VARCHAR(36) NOT NULL, owner_firstname VARCHAR(255) NOT NULL, owner_lastname VARCHAR(255) NOT NULL, owner_email VARCHAR(255) NOT NULL, tasks_count INT NOT NULL, pending_requests_count INT NOT NULL, participants_count INT NOT NULL, PRIMARY KEY(id, user_id))');
        $this->addSql('CREATE INDEX IDX_14CB9C5A76ED395 ON project_projections (user_id)');
        $this->addSql('CREATE INDEX IDX_14CB9C57E3C61F9 ON project_projections (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project_projections');
    }
}
