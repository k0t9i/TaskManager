<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\DeleteLinkCommand;
use App\Tasks\Domain\Exception\TaskNotExistException;
use App\Tasks\Domain\Repository\TaskRepositoryInterface;

class DeleteLinkCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteLinkCommand $command): void
    {
        $fromTaskId = new TaskId($command->fromTaskId);
        $task = $this->taskRepository->findById($fromTaskId);
        if ($task === null) {
            throw new TaskNotExistException();
        }

        $task->deleteLink(
            new TaskId($command->toTaskId),
            new UserId($command->currentUserId)
        );

        $this->taskRepository->save($task);
        $this->eventBus->dispatch(...$task->releaseEvents());
    }
}
