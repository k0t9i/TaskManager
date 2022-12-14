<?php

declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Application\Command\CloseTaskCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\ClosedTaskStatus;

final class CloseTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(CloseTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);
        if (null === $manager) {
            throw new TaskManagerNotExistException();
        }

        $manager->changeTaskStatus(
            $taskId,
            new ClosedTaskStatus(),
            $this->authenticator->getAuthUser()->getId(),
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
