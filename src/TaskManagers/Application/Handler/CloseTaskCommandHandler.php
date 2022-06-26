<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Application\CQ\CloseTaskCommand;
use App\TaskManagers\Domain\Exception\TaskManagerNotExistException;
use App\TaskManagers\Domain\Repository\TaskManagerRepositoryInterface;
use App\TaskManagers\Domain\ValueObject\ClosedTaskStatus;
use App\TaskManagers\Domain\ValueObject\TaskId;

class CloseTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CloseTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $manager->changeTaskStatus(
            $taskId,
            new ClosedTaskStatus(),
            new UserId($command->currentUserId),
        );

        $this->managerRepository->update($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}