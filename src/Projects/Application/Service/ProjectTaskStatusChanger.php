<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\DTO\ProjectDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Factory\ProjectFactory;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\TaskId;

final class ProjectTaskStatusChanger
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory
    ) {
    }

    public function changeStatus(Project $project, string $taskId, int $status): Project
    {
        $task = $project->getTasks()->getByTaskId(new TaskId($taskId));
        $tasks = $project->getTasks();
        // TODO add exception?
        if ($task !== null) {
            $taskDto = new ProjectTaskDTO(
                $task->getTaskId()->value,
                $status,
                $task->getOwnerId()->value,
                $task->getStartDate()->getValue(),
                $task->getFinishDate()->getValue(),
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
