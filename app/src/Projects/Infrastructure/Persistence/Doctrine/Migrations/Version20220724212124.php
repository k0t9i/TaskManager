<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220724212124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_participants (user_id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, PRIMARY KEY(user_id, project_id))');
        $this->addSql('CREATE INDEX IDX_5BCE94F1A76ED395 ON project_participants (user_id)');
        $this->addSql('CREATE INDEX IDX_5BCE94F1166D1F9C ON project_participants (project_id)');
        $this->addSql('CREATE TABLE project_tasks (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) DEFAULT NULL, task_id VARCHAR(36) NOT NULL, owner_id VARCHAR(36) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_430D6C09166D1F9C ON project_tasks (project_id)');
        $this->addSql('CREATE INDEX IDX_430D6C098DB60186 ON project_tasks (task_id)');
        $this->addSql('CREATE INDEX IDX_430D6C097E3C61F9 ON project_tasks (owner_id)');
        $this->addSql('CREATE TABLE projects (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, finish_date DATE NOT NULL, status INT NOT NULL, owner_id VARCHAR(36) NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C93B3A47E3C61F9 ON projects (owner_id)');
        $this->addSql('ALTER TABLE project_participants ADD CONSTRAINT FK_5BCE94F1166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_tasks ADD CONSTRAINT FK_430D6C09166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project_participants DROP CONSTRAINT FK_5BCE94F1166D1F9C');
        $this->addSql('ALTER TABLE project_tasks DROP CONSTRAINT FK_430D6C09166D1F9C');
        $this->addSql('DROP TABLE project_participants');
        $this->addSql('DROP TABLE project_tasks');
        $this->addSql('DROP TABLE projects');
    }
}
