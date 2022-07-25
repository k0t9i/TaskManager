<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220725111648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_links (to_task_id VARCHAR(36) NOT NULL, task_id VARCHAR(36) NOT NULL, PRIMARY KEY(to_task_id, task_id))');
        $this->addSql('CREATE INDEX IDX_1626BA3832E3C73 ON task_links (to_task_id)');
        $this->addSql('CREATE INDEX IDX_1626BA388DB60186 ON task_links (task_id)');
        $this->addSql('CREATE TABLE task_manager_participants (user_id VARCHAR(36) NOT NULL, manager_id VARCHAR(36) NOT NULL, PRIMARY KEY(user_id, manager_id))');
        $this->addSql('CREATE INDEX IDX_ECD390F2A76ED395 ON task_manager_participants (user_id)');
        $this->addSql('CREATE INDEX IDX_ECD390F2783E3463 ON task_manager_participants (manager_id)');
        $this->addSql('CREATE TABLE task_managers (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, status INT NOT NULL, owner_id VARCHAR(36) NOT NULL, finish_date DATE NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9890A4A166D1F9C ON task_managers (project_id)');
        $this->addSql('CREATE INDEX IDX_E9890A4A7E3C61F9 ON task_managers (owner_id)');
        $this->addSql('CREATE TABLE tasks (id VARCHAR(36) NOT NULL, manager_id VARCHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, brief TEXT NOT NULL, description TEXT NOT NULL, start_date DATE NOT NULL, finish_date DATE NOT NULL, owner_id VARCHAR(36) NOT NULL, status INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_50586597783E3463 ON tasks (manager_id)');
        $this->addSql('CREATE INDEX IDX_505865977E3C61F9 ON tasks (owner_id)');
        $this->addSql('ALTER TABLE task_links ADD CONSTRAINT FK_1626BA388DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_manager_participants ADD CONSTRAINT FK_ECD390F2783E3463 FOREIGN KEY (manager_id) REFERENCES task_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597783E3463 FOREIGN KEY (manager_id) REFERENCES task_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_manager_participants DROP CONSTRAINT FK_ECD390F2783E3463');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597783E3463');
        $this->addSql('ALTER TABLE task_links DROP CONSTRAINT FK_1626BA388DB60186');
        $this->addSql('DROP TABLE task_links');
        $this->addSql('DROP TABLE task_manager_participants');
        $this->addSql('DROP TABLE task_managers');
        $this->addSql('DROP TABLE tasks');
    }
}
