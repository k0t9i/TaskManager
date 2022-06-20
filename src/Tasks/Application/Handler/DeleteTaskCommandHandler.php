<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Tasks\Application\CQ\DeleteTaskCommand;
use App\Tasks\Domain\ValueObject\TaskId;
use App\Users\Domain\ValueObject\UserId;

class DeleteTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $project = $this->projectRepository->getById(new ProjectId($command->projectId));

        $project->deleteTask(
            new TaskId($command->id),
            new UserId($command->currentUserId),
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}