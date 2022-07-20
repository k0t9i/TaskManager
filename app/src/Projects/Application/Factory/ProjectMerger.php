<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

use App\Projects\Application\DTO\ProjectDTO;
use App\Projects\Application\DTO\ProjectMergeDTO;
use App\Projects\Domain\Entity\Project;

final class ProjectMerger
{
    public function __construct(private readonly ProjectFactory $factory)
    {
    }

    public function merge(Project $source, ProjectMergeDTO $dto) : Project {
        $info = $source->getInformation();
        return $this->factory->create(new ProjectDTO(
            $dto->id ?? $source->getId()->value,
            $dto->name ?? $info->name->value,
            $dto->description ?? $info->description->value,
            $dto->finishDate ?? $info->finishDate->getValue(),
            $dto->status ?? $source->getStatus()->getScalar(),
            $dto->ownerId ?? $source->getOwner()->userId->value,
            $dto->participantIds ?? $source->getParticipants()->getInnerItems(),
            $dto->tasks ?? $source->getTasks()->getInnerItems(),
        ));
    }
}
