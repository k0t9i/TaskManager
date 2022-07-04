<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Application\Command\CreateTaskCommand;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskBrief;
use App\Tasks\Domain\ValueObject\TaskDescription;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskName;
use App\Tasks\Domain\ValueObject\TaskOwner;

class CreateTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($command->projectId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
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
            new TaskOwner(
                new UserId($command->ownerId),
                new Email($command->ownerEmail)
            ),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}