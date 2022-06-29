<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\CloseProjectCommand;
use App\Projects\Domain\Exception\ProjectNotExistException;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\UserId;

final class CloseProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CloseProjectCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->projectId));
        if ($project === null) {
            throw new ProjectNotExistException();
        }

        $project->changeStatus(
            new ClosedProjectStatus(),
            new UserId($command->currentUserId)
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}