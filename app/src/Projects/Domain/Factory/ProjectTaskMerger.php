<?php
declare(strict_types=1);

namespace App\Projects\Domain\Factory;

use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\DTO\ProjectTaskMergeDTO;
use App\Projects\Domain\Entity\ProjectTask;

final class ProjectTaskMerger
{
    public function __construct(private readonly ProjectTaskFactory $factory)
    {
    }

    public function merge(ProjectTask $source, ProjectTaskMergeDTO $dto) : ProjectTask {
        return $this->factory->create(
            $source->getId()->value,
            new ProjectTaskDTO(
                $dto->taskId ?? $source->getId()->value,
                $dto->status ?? $source->getStatus()->getScalar(),
                $dto->ownerId ?? $source->getOwnerId()->value,
                $dto->startDate ?? $source->getStartDate()->getValue(),
                $dto->finishDate ?? $source->getFinishDate()->getValue(),
            )
        );
    }
}
