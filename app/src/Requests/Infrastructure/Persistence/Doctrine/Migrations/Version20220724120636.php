<?php

declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220724120636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_manager_participants (user_id VARCHAR(36) NOT NULL, manager_id VARCHAR(36) NOT NULL, PRIMARY KEY(user_id, manager_id))');
        $this->addSql('CREATE INDEX IDX_68BFDD08A76ED395 ON request_manager_participants (user_id)');
        $this->addSql('CREATE INDEX IDX_68BFDD08783E3463 ON request_manager_participants (manager_id)');
        $this->addSql('CREATE TABLE request_managers (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, status INT NOT NULL, owner_id VARCHAR(36) NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C6C0023166D1F9C ON request_managers (project_id)');
        $this->addSql('CREATE INDEX IDX_5C6C00237E3C61F9 ON request_managers (owner_id)');
        $this->addSql('CREATE TABLE requests (id VARCHAR(36) NOT NULL, manager_id VARCHAR(36) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, status INT NOT NULL, change_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B85D651A76ED395 ON requests (user_id)');
        $this->addSql('CREATE INDEX IDX_7B85D651783E3463 ON requests (manager_id)');
        $this->addSql('ALTER TABLE request_manager_participants ADD CONSTRAINT FK_68BFDD08783E3463 FOREIGN KEY (manager_id) REFERENCES request_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651783E3463 FOREIGN KEY (manager_id) REFERENCES request_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE request_manager_participants DROP CONSTRAINT FK_68BFDD08783E3463');
        $this->addSql('ALTER TABLE requests DROP CONSTRAINT FK_7B85D651783E3463');
        $this->addSql('DROP TABLE request_manager_participants');
        $this->addSql('DROP TABLE request_managers');
        $this->addSql('DROP TABLE requests');
    }
}
