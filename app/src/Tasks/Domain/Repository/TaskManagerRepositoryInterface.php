<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\Entity\TaskManager;

interface TaskManagerRepositoryInterface
{
    /**
     * @param ProjectId $id
     * @return TaskManager[]
     */
    public function findByProjectId(ProjectId $id): ?TaskManager;
    public function findByTaskId(TaskId $id): ?TaskManager;
    public function save(TaskManager $manager): void;
}
