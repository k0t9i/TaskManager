<?php
declare(strict_types=1);

namespace App\Projects\Domain\Factory;

use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTaskFactory
{
    public function create(string $id, ProjectTaskDTO $dto): ProjectTask
    {
        return new ProjectTask(
            new ProjectTaskId($id),
            new TaskId($dto->taskId),
            new UserId($dto->ownerId)
        );
    }
}
