<?php
declare(strict_types=1);

namespace App\ProjectTasks\Application\Handler;

use App\ProjectTasks\Application\CQ\DeleteTaskCommand;
use App\ProjectTasks\Domain\Repository\ProjectTaskRepositoryInterface;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

class DeleteTaskCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectTaskRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $project = $this->projectRepository->findByTaskId($taskId);

        $project->deleteTask(
            $taskId,
            new UserId($command->currentUserId),
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}