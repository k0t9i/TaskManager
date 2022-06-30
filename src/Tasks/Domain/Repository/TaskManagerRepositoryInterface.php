<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\ValueObject\TaskManagerId;

interface TaskManagerRepositoryInterface
{
    public function findById(TaskManagerId $id): ?TaskManager;
    public function findByProjectId(ProjectId $id): ?TaskManager;
    public function findByTaskId(TaskId $id): ?TaskManager;
    public function save(TaskManager $task): void;
}
