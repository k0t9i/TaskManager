<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Application\CQ\UpdateTaskInformationCommand;
use App\TaskManagers\Domain\Repository\TaskManagerRepositoryInterface;
use App\TaskManagers\Domain\ValueObject\TaskBrief;
use App\TaskManagers\Domain\ValueObject\TaskDescription;
use App\TaskManagers\Domain\ValueObject\TaskId;
use App\TaskManagers\Domain\ValueObject\TaskInformation;
use App\TaskManagers\Domain\ValueObject\TaskName;

class UpdateTaskInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateTaskInformationCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);

        $manager->changeTaskInformation(
            $taskId,
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new DateTime($command->startDate),
                new DateTime($command->finishDate)
            ),
            new UserId($command->currentUserId),
        );

        $this->managerRepository->update($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}