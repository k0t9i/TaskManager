<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Application\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;

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
