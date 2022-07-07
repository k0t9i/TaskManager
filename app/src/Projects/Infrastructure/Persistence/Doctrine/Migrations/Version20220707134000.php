<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220707134000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add version to projects';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD COLUMN version INT NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP COLUMN version');
    }
}
