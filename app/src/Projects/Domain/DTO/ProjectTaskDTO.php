<?php
declare(strict_types=1);

namespace App\Projects\Domain\DTO;

final class ProjectTaskDTO
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $ownerId
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['task_id'],
            $item['owner_id']
        );
    }
}
