<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Application\CQ\DeleteTaskCommand;
use App\TaskManagers\Domain\Exception\TaskManagerNotExistException;
use App\TaskManagers\Domain\Repository\TaskManagerRepositoryInterface;
use App\TaskManagers\Domain\ValueObject\TaskId;

class DeleteTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $manager->deleteTask(
            $taskId,
            new UserId($command->currentUserId),
        );

        $this->managerRepository->update($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}