<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Repository;

use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskManagerId;

class TaskManagerRepository implements TaskManagerRepositoryInterface
{

    public function findById(TaskManagerId $id): ?TaskManager
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function findByProjectId(ProjectId $id): ?TaskManager
    {
        // TODO: Implement findByProjectId() method.
        return null;
    }

    public function findByTaskId(TaskId $id): ?TaskManager
    {
        // TODO: Implement findByTaskId() method.
        return null;
    }

    public function save(TaskManager $task): void
    {
        // TODO: Implement save() method.
    }
}
