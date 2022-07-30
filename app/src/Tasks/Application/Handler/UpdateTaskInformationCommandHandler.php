<?php

declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\TaskNotExistException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Application\Command\UpdateTaskInformationCommand;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;

final class UpdateTaskInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(UpdateTaskInformationCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);
        if (null === $manager) {
            throw new TaskManagerNotExistException();
        }
        /** @var Task $task */
        $task = $manager->getTasks()->get($taskId);
        if (null === $task) {
            throw new TaskNotExistException($command->id);
        }

        $prevInfo = $task->getInformation();
        $manager->changeTaskInformation(
            $taskId,
            new TaskInformation(
                new TaskName($command->name ?? $prevInfo->name->value),
                new TaskBrief($command->brief ?? $prevInfo->brief->value),
                new TaskDescription($command->description ?? $prevInfo->description->value),
                new DateTime($command->startDate ?? $prevInfo->startDate->getValue()),
                new DateTime($command->finishDate ?? $prevInfo->finishDate->getValue())
            ),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
