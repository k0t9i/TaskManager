<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;

final class TaskFactory
{
    public function create(TaskDTO $dto): Task
    {
        return new Task(
            new TaskId($dto->id),
            new TaskInformation(
                new TaskName($dto->name),
                new TaskBrief($dto->brief),
                new TaskDescription($dto->description),
                new DateTime($dto->startDate),
                new DateTime($dto->finishDate),
            ),
            new Owner(
                new UserId($dto->ownerId),
                new Email($dto->ownerEmail)
            ),
            TaskStatusFactory::objectFromScalar($dto->status),
            $dto->links
        );
    }
}
