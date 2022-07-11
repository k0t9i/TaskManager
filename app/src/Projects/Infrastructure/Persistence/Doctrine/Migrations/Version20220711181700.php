<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220711181700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove fields from project tasks';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project_tasks DROP COLUMN status');
        $this->addSql('ALTER TABLE project_tasks DROP COLUMN start_date');
        $this->addSql('ALTER TABLE project_tasks DROP COLUMN finish_date');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project_tasks ADD COLUMN status INT');
        $this->addSql('ALTER TABLE project_tasks ADD COLUMN start_date DATE');
        $this->addSql('ALTER TABLE project_tasks ADD COLUMN finish_date DATE');
    }
}
