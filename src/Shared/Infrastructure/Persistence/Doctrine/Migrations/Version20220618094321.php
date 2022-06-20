<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220618094321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projects (id VARCHAR(36) NOT NULL, finish_date DATE NOT NULL, status INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, owner_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C93B3A47E3C61F9 ON projects (owner_id)');
        $this->addSql('COMMENT ON COLUMN projects.finish_date IS \'(DC2Type:projectFinishDate)\'');
        $this->addSql('CREATE TABLE tasks (id VARCHAR(36) NOT NULL, owner_id VARCHAR(255) DEFAULT NULL, project_id VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, finish_date DATE NOT NULL, status INT NOT NULL, name VARCHAR(255) NOT NULL, brief TEXT NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_505865977E3C61F9 ON tasks (owner_id)');
        $this->addSql('CREATE INDEX IDX_50586597166D1F9C ON tasks (project_id)');
        $this->addSql('COMMENT ON COLUMN tasks.start_date IS \'(DC2Type:taskStartDate)\'');
        $this->addSql('COMMENT ON COLUMN tasks.finish_date IS \'(DC2Type:taskFinishDate)\'');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_505865977E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A47E3C61F9');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597166D1F9C');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_505865977E3C61F9');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE users');
    }
}
