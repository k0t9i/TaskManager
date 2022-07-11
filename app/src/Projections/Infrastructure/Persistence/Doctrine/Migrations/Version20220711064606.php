<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711064606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project_projection (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, finish_date VARCHAR(255) NOT NULL, status INT NOT NULL, owner_id VARCHAR(255) NOT NULL, owner_firstname VARCHAR(255) NOT NULL, owner_lastname VARCHAR(255) NOT NULL, owner_email VARCHAR(255) NOT NULL, tasks_count INT NOT NULL, pending_requests_count INT NOT NULL, participants_count INT NOT NULL, PRIMARY KEY(id, user_id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project_projection');
    }
}
