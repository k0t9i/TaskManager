<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\ValueObject\TaskManagerId;

final class TaskManagerFactory
{
    public function create(TaskManagerDTO $dto) : TaskManager
    {
        return new TaskManager(
            new TaskManagerId($dto->id),
            new ProjectId($dto->projectId),
            ProjectStatusFactory::objectFromScalar($dto->status),
            new Owner(new UserId($dto->ownerId)),
            new DateTime($dto->finishDate),
            new Participants($dto->participantIds),
            $dto->tasks
        );
    }
}
