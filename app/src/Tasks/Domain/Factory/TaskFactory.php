<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskStatus;

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
            new UserId($dto->ownerId),
            TaskStatus::createFromScalar($dto->status),
            $dto->links
        );
    }
}
