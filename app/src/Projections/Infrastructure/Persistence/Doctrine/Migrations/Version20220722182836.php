<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220722182836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_link_projections (id VARCHAR(36) NOT NULL, to_id VARCHAR(36) NOT NULL, PRIMARY KEY(id, to_id))');
        $this->addSql('
            CREATE VIEW v_task_link_projections AS
                SELECT t.id owner_task_id, p.* FROM task_link_projections t
                LEFT JOIN v_task_projections p ON t.to_id = p.id
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW v_task_link_projections');
        $this->addSql('DROP TABLE task_link_projections');
    }
}
