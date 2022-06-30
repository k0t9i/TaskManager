<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($command->projectId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }
        $user = $this->userRepository->findById(new UserId($command->userId));
        if ($user === null) {
            throw new UserNotExistException();
        }

        $manager->createRequest(
            new RequestId($this->uuidGenerator->generate()),
            $user->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}