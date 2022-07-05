<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($command->projectId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $manager->createRequest(
            new RequestId($this->uuidGenerator->generate()),
            new Owner(
                new UserId($command->userId),
                new Email($command->userEmail)
            )
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}