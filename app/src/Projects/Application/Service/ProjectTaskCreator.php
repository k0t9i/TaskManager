<?php
declare(strict_types=1);

namespace App\Projects\Application\Service;

use App\Projects\Domain\DTO\ProjectDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Factory\ProjectFactory;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Service\UuidGeneratorInterface;

final class ProjectTaskCreator
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory,
        private readonly UuidGeneratorInterface $uuidGenerator,
    ) {
    }

    public function createTask(Project $project, ProjectTaskDTO $taskDto): Project
    {
        $tasks = $project->getTasks()->add(
            $this->projectTaskFactory->create($this->uuidGenerator->generate(), $taskDto)
        );

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
