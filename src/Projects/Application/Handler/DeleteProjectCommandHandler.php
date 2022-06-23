<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\DeleteProjectCommand;
use App\Projects\Domain\Event\ProjectWasDeletedEvent;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

final class DeleteProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteProjectCommand $command): void
    {
        $project = $this->repository->getById(new ProjectId($command->projectId));

        $project->ensureIsOwner(new UserId($command->currentUserId));
        $this->repository->delete($project);
        $project->registerEvent(new ProjectWasDeletedEvent($project->getId()->value));

        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}