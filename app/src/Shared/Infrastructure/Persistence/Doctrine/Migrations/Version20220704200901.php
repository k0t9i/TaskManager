<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704200901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add owner email to projects';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD COLUMN owner_email VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP COLUMN owner_email');
    }
}
