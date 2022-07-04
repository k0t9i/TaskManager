<?php
declare(strict_types=1);

namespace App\Projects\Domain\DTO;

final class ProjectTaskDTO
{
    public function __construct(
        public readonly string $taskId,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly string $startDate,
        public readonly string $finishDate
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['task_id'],
            $item['status'],
            $item['owner_id'],
            $item['start_date'],
            $item['finish_date']
        );
    }
}
