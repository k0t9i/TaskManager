<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\ActiveTaskStatus;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\ActivateTaskCommand;
use App\Tasks\Domain\Exception\TaskNotExistException;
use App\Tasks\Domain\Repository\TaskRepositoryInterface;

class ActivateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ActivateTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $task = $this->taskRepository->findById($taskId);
        if ($task === null) {
            throw new TaskNotExistException();
        }

        $task->changeStatus(
            new ActiveTaskStatus(),
            new UserId($command->currentUserId),
        );

        $this->taskRepository->save($task);
        $this->eventBus->dispatch(...$task->releaseEvents());
    }
}