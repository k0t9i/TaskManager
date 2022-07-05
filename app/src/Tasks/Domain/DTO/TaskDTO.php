<?php
declare(strict_types=1);

namespace App\Tasks\Domain\DTO;

use App\Tasks\Domain\Collection\TaskLinkCollection;

final class TaskDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly int $status,
        public readonly TaskLinkCollection $links
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['id'],
            $item['name'],
            $item['brief'],
            $item['description'],
            $item['start_date'],
            $item['finish_date'],
            $item['owner_id'],
            $item['status'],
            $item['links']
        );
    }
}
