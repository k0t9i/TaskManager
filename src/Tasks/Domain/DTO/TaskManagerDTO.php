<?php
declare(strict_types=1);

namespace App\Tasks\Domain\DTO;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Tasks\Domain\Collection\TaskCollection;

final class TaskManagerDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly string $finishDate,
        public readonly UserIdCollection $participantIds,
        public readonly TaskCollection $tasks
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['id'],
            $item['project_id'],
            $item['status'],
            $item['owner_id'],
            $item['finish_date'],
            $item['participant_ids'],
            $item['tasks']
        );
    }
}
