<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\Repository;

use App\Shared\Domain\ValueObject\TaskId;
use App\TaskManagers\Domain\Entity\TaskManager;
use App\TaskManagers\Domain\ValueObject\TaskManagerId;

interface TaskManagerRepositoryInterface
{
    public function findById(TaskManagerId $id): ?TaskManager;
    public function findByTaskId(TaskId $id): ?TaskManager;
    public function update(TaskManager $projectTask): void;
}
