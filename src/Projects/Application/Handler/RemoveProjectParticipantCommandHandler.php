<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\RemoveProjectParticipantCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectParticipant;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;

final class RemoveProjectParticipantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RemoveProjectParticipantCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));
        $participant = $this->userRepository->getById(new UserId($command->participantId));

        $project->removeParticipant(
            new ProjectParticipant($participant->getId()),
            new UserId($command->currentUserId)
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}