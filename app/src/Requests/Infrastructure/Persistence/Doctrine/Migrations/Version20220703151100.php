<?php

declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220703151100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add request tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE request_managers (
                id VARCHAR(36) NOT NULL,
                project_id VARCHAR(36) NOT NULL,
                status INT NOT NULL,
                owner_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_request_managers_project ON request_managers (project_id)');
        $this->addSql('CREATE INDEX idx_request_managers_owner ON request_managers (owner_id)');

        $this->addSql('
            CREATE TABLE request_manager_participants (
                request_manager_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                PRIMARY KEY(request_manager_id, user_id)
            )
        ');
        $this->addSql('CREATE INDEX idx_request_manager_participants_request_manager ON request_manager_participants (request_manager_id)');
        $this->addSql('CREATE INDEX idx_request_manager_participants_user ON request_manager_participants (user_id)');
        $this->addSql('
            ALTER TABLE request_manager_participants ADD CONSTRAINT fk_request_manager_participants_request_manager
                FOREIGN KEY (request_manager_id) REFERENCES request_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');

        $this->addSql('
            CREATE TABLE requests (
                id VARCHAR(36) NOT NULL,
                request_manager_id VARCHAR(36) NOT NULL,
                user_id VARCHAR(36) NOT NULL,
                status INT NOT NULL,
                change_date TIMESTAMP NOT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_requests_request_manager ON requests (request_manager_id)');
        $this->addSql('CREATE INDEX idx_requests_user ON requests (user_id)');
        $this->addSql('
            ALTER TABLE requests ADD CONSTRAINT fk_requests_request_manager
                FOREIGN KEY (request_manager_id) REFERENCES request_managers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE requests');
        $this->addSql('DROP TABLE request_manager_participants');
        $this->addSql('DROP TABLE request_managers');
    }
}
