<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Domain\Entity\Project;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectParticipantAdder
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
    ) {
    }

    public function addParticipant(Project $project, string $participantId): Project
    {
        $participants = $project->getParticipants()->add(new UserId($participantId));

        $dto = new ProjectDTO(
            $project->getId()->value,
            $project->getInformation()->name->value,
            $project->getInformation()->description->value,
            $project->getInformation()->finishDate->getValue(),
            ProjectStatusFactory::scalarFromObject($project->getStatus()),
            $project->getOwner()->userId->value,
            $participants->getInnerItems(),
            $project->getTasks()->getInnerItems()
        );
        return $this->projectFactory->create($dto);
    }
}
