<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\ClosedTaskStatus;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\CloseTaskCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

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

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}