<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Tasks\Application\Command\DeleteLinkCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

class DeleteLinkCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(DeleteLinkCommand $command): void
    {
        $fromTaskId = new TaskId($command->fromTaskId);
        $manager = $this->managerRepository->findByTaskId($fromTaskId);
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $manager->deleteTaskLink(
            $fromTaskId,
            new TaskId($command->toTaskId),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
