<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\ValueObject\ProjectTaskId;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectTaskCreator
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly UuidGeneratorInterface $uuidGenerator
    ) {
    }

    public function createTask(Project $project, ProjectTaskDTO $taskDto): Project
    {
        $tasks = $project->getTasks()->add(new ProjectTask(
            new ProjectTaskId($this->uuidGenerator->generate()),
            new TaskId($taskDto->taskId),
            TaskStatusFactory::objectFromScalar($taskDto->status),
            new UserId($taskDto->ownerId),
            new DateTime($taskDto->startDate),
            new DateTime($taskDto->finishDate),
        ));

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
