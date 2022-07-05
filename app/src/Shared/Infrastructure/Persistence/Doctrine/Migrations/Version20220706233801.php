<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706233801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove email to tasks, requests and projects';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP COLUMN owner_email');
        $this->addSql('ALTER TABLE requests DROP COLUMN user_email');
        $this->addSql('ALTER TABLE tasks DROP COLUMN owner_email');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD COLUMN owner_email VARCHAR(255)');
        $this->addSql('ALTER TABLE requests ADD COLUMN user_email VARCHAR(255)');
        $this->addSql('ALTER TABLE projects ADD COLUMN owner_email VARCHAR(255)');
    }
}
