<?php

declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\SharedBoundedContext\Domain\Repository\SharedUserRepositoryInterface;
use App\Tasks\Application\Command\CreateTaskCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;

final class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly SharedUserRepositoryInterface $userRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($command->projectId));
        if (null === $manager) {
            throw new TaskManagerNotExistException();
        }
        $userId = null !== $command->ownerId
            ? new UserId($command->ownerId)
            : $this->authenticator->getAuthUser()->getId();
        $user = $this->userRepository->findById($userId->value);
        if (null === $user) {
            throw new UserNotExistException($userId->value);
        }

        $manager->createTask(
            new TaskId($command->id),
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new DateTime($command->startDate),
                new DateTime($command->finishDate)
            ),
            $userId,
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
