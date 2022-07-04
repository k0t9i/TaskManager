<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\ChangeProjectOwnerCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Exception;

final class ChangeProjectOwnerCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    /**
     * @param ChangeProjectOwnerCommand $command
     * @throws Exception
     */
    public function __invoke(ChangeProjectOwnerCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->id));
        if ($project === null) {
            throw new ProjectNotExistException($command->id);
        }
        //TODO what can I do with using of user repository here?
        $user = $this->userRepository->findById(new UserId($command->ownerId));
        if ($user === null) {
            throw new UserNotExistException($command->ownerId);
        }

        $project->changeOwner(
            $user->getId(),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}