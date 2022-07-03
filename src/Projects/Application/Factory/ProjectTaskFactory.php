<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Application\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTaskFactory
{
    public function create(string $id, ProjectTaskDTO $dto): ProjectTask
    {
        return new ProjectTask(
            new ProjectTaskId($id),
            new TaskId($dto->taskId),
            TaskStatusFactory::objectFromScalar($dto->status),
            new UserId($dto->ownerId),
            new DateTime($dto->startDate),
            new DateTime($dto->finishDate)
        );
    }
}
