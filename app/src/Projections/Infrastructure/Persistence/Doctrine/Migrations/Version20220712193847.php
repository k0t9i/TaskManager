<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712193847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE task_projections (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, brief TEXT NOT NULL, description TEXT NOT NULL, start_date DATE NOT NULL, finish_date DATE NOT NULL, status INT NOT NULL, owner_id VARCHAR(36) NOT NULL, owner_firstname VARCHAR(255) NOT NULL, owner_lastname VARCHAR(255) NOT NULL, owner_email VARCHAR(255) NOT NULL, links_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FF6E30C166D1F9C ON task_projections (project_id)');
        $this->addSql('CREATE INDEX IDX_8FF6E30C7E3C61F9 ON task_projections (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE task_projections');
    }
}
