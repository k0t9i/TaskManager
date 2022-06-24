<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Repository;

use App\ProjectTasks\Domain\Entity\ProjectTask;
use App\ProjectTasks\Domain\ValueObject\ProjectTaskId;
use App\ProjectTasks\Domain\ValueObject\TaskId;

interface ProjectTaskRepositoryInterface
{
    public function findById(ProjectTaskId $id): ?ProjectTask;
    public function findByTaskId(TaskId $id): ?ProjectTask;
    public function update(ProjectTask $projectTask): void;
}
