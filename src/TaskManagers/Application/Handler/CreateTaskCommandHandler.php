<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\TaskManagers\Application\Command\CreateTaskCommand;
use App\TaskManagers\Domain\Exception\TaskManagerNotExistException;
use App\TaskManagers\Domain\Repository\TaskManagerRepositoryInterface;
use App\TaskManagers\Domain\ValueObject\TaskBrief;
use App\TaskManagers\Domain\ValueObject\TaskDescription;
use App\TaskManagers\Domain\ValueObject\TaskInformation;
use App\TaskManagers\Domain\ValueObject\TaskManagerId;
use App\TaskManagers\Domain\ValueObject\TaskName;
use App\Users\Domain\Repository\UserRepositoryInterface;

class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $manager = $this->managerRepository->findById(new TaskManagerId($command->projectId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }
        $taskOwner = $this->userRepository->findById(new UserId($command->ownerId));
        if ($taskOwner === null) {
            throw new UserNotExistException();
        }

        $manager->createTask(
            new TaskId($this->uuidGenerator->generate()),
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new DateTime($command->startDate),
                new DateTime($command->finishDate)
            ),
            $taskOwner->getId(),
            new UserId($command->currentUserId)
        );

        $this->managerRepository->update($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}