<?php
declare(strict_types=1);

namespace App\ProjectTasks\Application\Handler;

use App\ProjectTasks\Application\CQ\UpdateTaskInformationCommand;
use App\ProjectTasks\Domain\Repository\ProjectTaskRepositoryInterface;
use App\ProjectTasks\Domain\ValueObject\TaskBrief;
use App\ProjectTasks\Domain\ValueObject\TaskDescription;
use App\ProjectTasks\Domain\ValueObject\TaskFinishDate;
use App\ProjectTasks\Domain\ValueObject\TaskId;
use App\ProjectTasks\Domain\ValueObject\TaskInformation;
use App\ProjectTasks\Domain\ValueObject\TaskName;
use App\ProjectTasks\Domain\ValueObject\TaskStartDate;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

class UpdateTaskInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectTaskRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateTaskInformationCommand $command): void
    {
        $taskId = new TaskId($command->id);
        $project = $this->projectRepository->findByTaskId($taskId);

        $project->changeTaskInformation(
            $taskId,
            new TaskInformation(
                new TaskName($command->name),
                new TaskBrief($command->brief),
                new TaskDescription($command->description),
                new TaskStartDate($command->startDate),
                new TaskFinishDate($command->finishDate)
            ),
            new UserId($command->currentUserId),
        );

        $this->projectRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}