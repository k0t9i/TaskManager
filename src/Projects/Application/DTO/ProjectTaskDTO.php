<?php
declare(strict_types=1);

namespace App\Projects\Application\DTO;

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
}
