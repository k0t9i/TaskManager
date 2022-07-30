<?php

declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Domain\Entity\TaskManager;

interface TaskManagerRepositoryInterface
{
    /**
     * @return TaskManager[]
     */
    public function findByProjectId(ProjectId $id): ?TaskManager;
    public function findByTaskId(TaskId $id): ?TaskManager;
    public function save(TaskManager $manager): void;
}
