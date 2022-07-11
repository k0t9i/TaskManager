<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\RemoveProjectParticipantCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use Exception;

final class RemoveProjectParticipantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    /**
     * @param RemoveProjectParticipantCommand $command
     * @throws Exception
     */
    public function __invoke(RemoveProjectParticipantCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->id));
        if ($project === null) {
            throw new ProjectNotExistException($command->id);
        }

        $project->removeParticipant(
            new UserId($command->participantId),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}