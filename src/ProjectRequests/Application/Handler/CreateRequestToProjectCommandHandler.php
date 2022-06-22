<?php
declare(strict_types=1);

namespace App\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\CreateRequestToProjectCommand;
use App\ProjectRequests\Domain\Entity\Request;
use App\ProjectRequests\Domain\Entity\RequestProject;
use App\ProjectRequests\Domain\Repository\RequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestUser;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestRepositoryInterface $requestRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));
        $user = $this->userRepository->getById(new UserId($command->userId));

        $request = Request::create(
            new RequestId($this->uuidGenerator->generate()),
            new RequestProject(
                $project->getId(),
                $project->getName(),
                $project->getStatus(),
                $project->getOwner(),
                $project->getParticipants()
            ),
            new RequestUser(
                $user->getId(),
                $user->getFirstname(),
                $user->getLastname(),
                $user->getEmail()
            )
        );

        $this->requestRepository->create($request);
        $this->eventBus->dispatch(...$request->releaseEvents());
    }
}