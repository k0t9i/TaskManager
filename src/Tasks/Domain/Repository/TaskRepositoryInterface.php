<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\Entity\Task;

interface TaskRepositoryInterface
{
    public function findById(TaskId $id): ?Task;
    public function save(Task $task): void;

    /**
     * @param ProjectId $projectId
     * @return array|Task[]
     */
    public function findAllByProjectId(ProjectId $projectId): array;
}
