<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\RemoveProjectParticipantCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Exception;

final class RemoveProjectParticipantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
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
            throw new ProjectNotExistException();
        }
        $participant = $this->userRepository->findById(new UserId($command->participantId));
        if ($participant === null) {
            throw new UserNotExistException();
        }

        $project->removeParticipant(
            $participant->getId(),
            $this->authenticator->getAuthUser()->userId
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}