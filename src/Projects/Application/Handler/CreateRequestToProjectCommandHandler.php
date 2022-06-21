<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\ActivateProjectCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectRequestId;
use App\Projects\Domain\ValueObject\ProjectRequestUser;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ActivateProjectCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));
        $user = $this->userRepository->getById(new UserId($command->currentUserId));

        $project->createProjectRequest(
            new ProjectRequestId($this->uuidGenerator->generate()),
            new ProjectRequestUser(
                $user->getId(),
                $user->getFirstname(),
                $user->getLastname(),
                $user->getEmail()
            )
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}