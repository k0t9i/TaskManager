<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720123700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'lazybone';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE VIEW v_task_projections AS
                SELECT t.*, p.user_id FROM task_projections t
                LEFT JOIN project_projections p ON t.project_id = p.id
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW v_task_projections');
    }
}
