<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718095413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_projections (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) DEFAULT NULL, owner_id VARCHAR(36) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_609887AB166D1F9C ON user_projections (project_id)');
        $this->addSql('CREATE INDEX IDX_609887ABA76ED395 ON user_projections (user_id)');
        $this->addSql('CREATE INDEX IDX_609887AB7E3C61F9 ON user_projections (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_609887AB166D1F9CA76ED395 ON user_projections (project_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_projections');
    }
}
