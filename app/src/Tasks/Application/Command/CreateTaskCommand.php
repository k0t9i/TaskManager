<?php

declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class CreateTaskCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $projectId,
        public readonly ?string $ownerId = null,
    ) {
    }

    public static function createFromRequest(string $id, array $item, string $projectId, ?string $ownerId = null): self
    {
        return new self(
            $id,
            $item['name'] ?? '',
            $item['brief'] ?? '',
            $item['description'] ?? '',
            $item['start_date'] ?? '',
            $item['finish_date'] ?? '',
            $projectId,
            $ownerId,
        );
    }
}
