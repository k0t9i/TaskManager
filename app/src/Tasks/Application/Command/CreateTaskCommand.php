<?php
declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CreateTaskCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $projectId,
        public readonly string $ownerId,
        public readonly string $ownerEmail,
    ) {
    }

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['name'] ?? '',
            $item['brief'] ?? '',
            $item['description'] ?? '',
            $item['start_date'] ?? '',
            $item['finish_date'] ?? '',
            $item['project_id'],
            $item['owner_id'],
            $item['owner_email'],
        );
    }
}