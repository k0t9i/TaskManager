<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705163801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add email to tasks and requests';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks ADD COLUMN owner_email VARCHAR(255)');
        $this->addSql('ALTER TABLE requests ADD COLUMN user_email VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE requests DROP COLUMN user_email');
        $this->addSql('ALTER TABLE tasks DROP COLUMN owner_email');
    }
}
