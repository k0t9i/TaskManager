<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220704131700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add event table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE events (
                id VARCHAR(36) NOT NULL,
                aggregate_id VARCHAR(36) NOT NULL,
                name TEXT NOT NULL,
                body TEXT NOT NULL,
                occurred_on TIMESTAMP NOT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE events');
    }
}
