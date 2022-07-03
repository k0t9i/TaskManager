<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220703151000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add project tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE projects (
                id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                finish_date DATE NOT NULL,
                status INT NOT NULL,
                owner_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_projects_owner ON projects (owner_id)');

        $this->addSql('
            CREATE TABLE project_participants (
                project_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(project_id, user_id)
            )
        ');
        $this->addSql('CREATE INDEX idx_project_participants_project ON project_participants (project_id)');
        $this->addSql('CREATE INDEX idx_project_participants_user ON project_participants (user_id)');
        $this->addSql('
            ALTER TABLE project_participants ADD CONSTRAINT fk_project_participants_project
                FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');

        $this->addSql('
            CREATE TABLE project_tasks (
                id VARCHAR(36) NOT NULL,
                project_id VARCHAR(36) NOT NULL,
                task_id VARCHAR(36) NOT NULL,
                status INT NOT NULL,
                owner_id VARCHAR(36) NOT NULL,
                start_date DATE NOT NULL,
                finish_date DATE NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_project_tasks_project ON project_tasks (project_id)');
        $this->addSql('CREATE INDEX idx_project_tasks_task ON project_tasks (task_id)');
        $this->addSql('CREATE INDEX idx_project_tasks_owner ON project_tasks (owner_id)');
        $this->addSql('
            ALTER TABLE project_tasks ADD CONSTRAINT fk_project_tasks_project
                FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project_tasks');
        $this->addSql('DROP TABLE project_participants');
        $this->addSql('DROP TABLE projects');
    }
}
