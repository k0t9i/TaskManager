<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Projects\Domain\Entity\Project;

final class ProjectResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly string $ownerEmail,
        public readonly int $status,
        public readonly int $tasksCount,
        public readonly int $participantsCount,
    ) {
    }

    public static function createFromEntity(Project $project): self
    {
        return new self(
            $project->getId()->value,
            $project->getInformation()->name->value,
            $project->getInformation()->finishDate->getValue(),
            $project->getOwner()->userId->value,
            $project->getOwner()->userEmail->value,
            $project->getStatus()->getScalar(),
            count($project->getTasks()->getInnerItems()),
            count($project->getParticipants()->getInnerItems()),
        );
    }
}
