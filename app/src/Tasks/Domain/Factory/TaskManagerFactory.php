<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use App\Tasks\Domain\ValueObject\Tasks;

final class TaskManagerFactory
{
    public function create(TaskManagerDTO $dto) : TaskManager
    {
        return new TaskManager(
            new TaskManagerId($dto->id),
            new ProjectId($dto->projectId),
            ProjectStatus::createFromScalar($dto->status),
            new Owner(new UserId($dto->ownerId)),
            new DateTime($dto->finishDate),
            new Participants($dto->participantIds),
            new Tasks($dto->tasks)
        );
    }
}
