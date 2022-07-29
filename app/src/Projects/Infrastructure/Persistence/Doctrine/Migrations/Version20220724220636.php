<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220724220636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE requests (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, status INT NOT NULL, change_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B85D651A76ED395 ON requests (user_id)');
        $this->addSql('CREATE INDEX IDX_7B85D651783E3463 ON requests (project_id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651783E3463 FOREIGN KEY (project_id) REFERENCES projects (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE requests');
    }
}
