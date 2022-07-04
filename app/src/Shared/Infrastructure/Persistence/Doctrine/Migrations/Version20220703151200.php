<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220703151200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add task tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE task_managers (
                id VARCHAR(36) NOT NULL,
                project_id VARCHAR(36) NOT NULL,
                status INT NOT NULL,
                owner_id VARCHAR(36) NOT NULL,
                finish_date DATE NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_task_managers_project ON task_managers (project_id)');
        $this->addSql('CREATE INDEX idx_task_managers_owner ON task_managers (owner_id)');

        $this->addSql('
            CREATE TABLE task_manager_participants (
                task_manager_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(task_manager_id, user_id)
            )
        ');
        $this->addSql('CREATE INDEX idx_task_manager_participants_task_manager ON task_manager_participants (task_manager_id)');
        $this->addSql('CREATE INDEX idx_task_manager_participants_user ON task_manager_participants (user_id)');
        $this->addSql('
            ALTER TABLE task_manager_participants ADD CONSTRAINT fk_task_manager_participants_task_manager
                FOREIGN KEY (task_manager_id) REFERENCES task_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');

        $this->addSql('
            CREATE TABLE tasks (
                id VARCHAR(36) NOT NULL,
                task_manager_id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                brief TEXT NOT NULL,
                description TEXT NOT NULL,
                start_date DATE NOT NULL,
                finish_date DATE NOT NULL,
                owner_id VARCHAR(36) NOT NULL,
                status INT NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_tasks_task_manager ON tasks (task_manager_id)');
        $this->addSql('CREATE INDEX idx_tasks_owner ON tasks (owner_id)');
        $this->addSql('
            ALTER TABLE tasks ADD CONSTRAINT fk_tasks_task_manager
                FOREIGN KEY (task_manager_id) REFERENCES task_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');

        $this->addSql('
            CREATE TABLE task_links (
                from_task_id VARCHAR(36) NOT NULL,
                to_task_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(from_task_id, to_task_id)
            )
        ');
        $this->addSql('CREATE INDEX idx_task_links_from_task ON task_links (from_task_id)');
        $this->addSql('CREATE INDEX idx_task_links_to_task ON task_links (to_task_id)');
        $this->addSql('
            ALTER TABLE task_links ADD CONSTRAINT fk_task_links_from_task
                FOREIGN KEY (from_task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('
            ALTER TABLE task_links ADD CONSTRAINT fk_task_links_to_task
                FOREIGN KEY (to_task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE task_links');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE task_manager_participants');
        $this->addSql('DROP TABLE task_managers');
    }
}
