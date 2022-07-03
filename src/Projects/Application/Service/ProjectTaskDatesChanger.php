<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Application\DTO\ProjectDTO;
use App\Projects\Application\DTO\ProjectTaskDTO;
use App\Projects\Application\Factory\ProjectFactory;
use App\Projects\Application\Factory\ProjectTaskFactory;
use App\Projects\Domain\Entity\Project;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\TaskId;

final class ProjectTaskDatesChanger
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory
    ) {
    }

    public function changeDates(Project $project, string $taskId, string $startDate, string $finishDate): Project
    {
        $task = $project->getTasks()->getByTaskId(new TaskId($taskId));
        $tasks = $project->getTasks();
        // TODO add exception?
        if ($task !== null) {
            $taskDto = new ProjectTaskDTO(
                $task->getTaskId()->value,
                TaskStatusFactory::scalarFromObject($task->getStatus()),
                $task->getOwnerId()->value,
                $startDate,
                $finishDate,
            );
            $tasks = $tasks->add(
                $this->projectTaskFactory->create($task->getId()->value, $taskDto)
            );
        }

        $dto = new ProjectDTO(
            $project->getId()->value,
            $project->getInformation()->name->value,
            $project->getInformation()->description->value,
            $project->getInformation()->finishDate->getValue(),
            ProjectStatusFactory::scalarFromObject($project->getStatus()),
            $project->getOwner()->userId->value,
            $project->getParticipants()->getInnerItems(),
            $tasks->getInnerItems()
        );
        return $this->projectFactory->create($dto);
    }
}
