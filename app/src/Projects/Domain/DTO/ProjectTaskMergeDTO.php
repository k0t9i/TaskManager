<?php
declare(strict_types=1);

namespace App\Projects\Domain\DTO;

final class ProjectTaskMergeDTO
{
    public function __construct(
        public readonly ?string $taskId = null,
        public readonly ?int $status = null,
        public readonly ?string $ownerId = null,
        public readonly ?string $startDate = null,
        public readonly ?string $finishDate = null
    ) {
    }
}
