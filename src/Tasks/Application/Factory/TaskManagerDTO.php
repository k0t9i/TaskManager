<?php
declare(strict_types=1);

namespace App\Tasks\Application\Factory;

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
}
