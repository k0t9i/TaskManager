<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\CloseProjectCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ClosedProjectStatus;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Users\Domain\ValueObject\UserId;

final class CloseProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CloseProjectCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));

        $project->changeStatus(
            new ClosedProjectStatus(),
            new UserId($command->currentUserId)
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}