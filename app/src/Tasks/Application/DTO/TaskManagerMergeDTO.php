<?php
declare(strict_types=1);

namespace App\Tasks\Application\DTO;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Tasks\Domain\Collection\TaskCollection;

final class TaskManagerMergeDTO
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $projectId = null,
        public readonly ?int $status = null,
        public readonly ?string $ownerId = null,
        public readonly ?string $finishDate = null,
        public readonly ?UserIdCollection $participantIds = null,
        public readonly ?TaskCollection $tasks = null
    ) {
    }
}
