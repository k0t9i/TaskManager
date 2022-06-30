<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\UpdateTaskInformationCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;

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
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

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

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}