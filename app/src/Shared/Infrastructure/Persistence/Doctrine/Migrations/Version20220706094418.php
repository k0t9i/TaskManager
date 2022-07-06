<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706094418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add shared users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE shared_users (
              id VARCHAR(36) NOT NULL,
              email VARCHAR(255) NOT NULL,
              firstname VARCHAR(255) NOT NULL,
              lastname VARCHAR(255) NOT NULL
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE shared_users');
    }
}
