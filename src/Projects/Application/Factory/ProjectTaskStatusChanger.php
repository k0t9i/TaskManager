<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\TaskId;

final class ProjectTaskStatusChanger
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
    ) {
    }

    public function changeStatus(Project $project, string $taskId, int $status): Project
    {
        $task = $project->getTasks()->getByTaskId(new TaskId($taskId));
        $tasks = $project->getTasks();
        // TODO add exception?
        if ($task !== null) {
            $tasks = $tasks->add(new ProjectTask(
                $task->getId(),
                $task->getTaskId(),
                TaskStatusFactory::objectFromScalar($status),
                $task->getOwnerId(),
                $task->getStartDate(),
                $task->getFinishDate(),
            ));
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
