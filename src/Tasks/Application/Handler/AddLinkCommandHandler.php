<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Application\Command\AddLinkCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

class AddLinkCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(AddLinkCommand $command): void
    {
        $fromTaskId = new TaskId($command->fromTaskId);
        $manager = $this->managerRepository->findByTaskId($fromTaskId);
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $manager->createTaskLink(
            $fromTaskId,
            new TaskId($command->toTaskId),
            $this->authenticator->getAuthUser()->userId
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}
