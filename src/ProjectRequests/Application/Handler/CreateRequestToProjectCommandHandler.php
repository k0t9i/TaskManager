<?php
declare(strict_types=1);

namespace App\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\CreateRequestToProjectCommand;
use App\ProjectRequests\Domain\Repository\ProjectRequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRequestRepositoryInterface $projectRequestRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $project = $this->projectRequestRepository->getById(new ProjectRequestId($command->projectId));
        $user = $this->userRepository->getById(new UserId($command->userId));

        $project->createRequest(
            new RequestId($this->uuidGenerator->generate()),
            $user->getId()
        );

        $this->projectRequestRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}