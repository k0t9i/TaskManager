<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220618145022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_participant (project_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, PRIMARY KEY(project_id, user_id))');
        $this->addSql('CREATE INDEX IDX_B4021E51166D1F9C ON project_participant (project_id)');
        $this->addSql('CREATE INDEX IDX_B4021E51A76ED395 ON project_participant (user_id)');
        $this->addSql('ALTER TABLE project_participant ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_participant ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('ALTER TABLE users ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tasks ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ALTER owner_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE tasks ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ALTER project_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE tasks ALTER project_id DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ALTER status TYPE INT');
        $this->addSql('ALTER TABLE tasks ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE projects ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER status TYPE INT');
        $this->addSql('ALTER TABLE projects ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER owner_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE projects ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT fk_5c93b3a47e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5c93b3a47e3c61f9 ON projects (owner_id)');
    }
}
