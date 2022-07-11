<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Application\Command\ActivateTaskCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\ActiveTaskStatus;

class ActivateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(ActivateTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $manager = $this->managerRepository->findByTaskId($taskId);
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $manager->changeTaskStatus(
            $taskId,
            new ActiveTaskStatus(),
            $this->authenticator->getAuthUser()->getId(),
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}