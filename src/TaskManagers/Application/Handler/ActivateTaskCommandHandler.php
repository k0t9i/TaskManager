<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Application\CQ\ActivateTaskCommand;
use App\TaskManagers\Domain\Repository\TaskManagerRepositoryInterface;
use App\TaskManagers\Domain\ValueObject\ActiveTaskStatus;
use App\TaskManagers\Domain\ValueObject\TaskId;

class ActivateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ActivateTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);

        $manager->changeTaskStatus(
            $taskId,
            new ActiveTaskStatus(),
            new UserId($command->currentUserId),
        );

        $this->managerRepository->update($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}