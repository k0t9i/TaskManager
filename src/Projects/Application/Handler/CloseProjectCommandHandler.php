<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\CloseProjectCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\ProjectId;

final class CloseProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(CloseProjectCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->id));
        if ($project === null) {
            throw new ProjectNotExistException();
        }

        $project->changeStatus(
            new ClosedProjectStatus(),
            $this->authenticator->getAuthUser()->userId
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}