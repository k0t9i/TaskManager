<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\DTO\TaskManagerMergeDTO;
use App\Tasks\Domain\Entity\TaskManager;

final class TaskManagerMerger
{
    public function __construct(private readonly TaskManagerFactory $factory)
    {
    }

    public function merge(TaskManager $source, TaskManagerMergeDTO $dto): TaskManager
    {
        return $this->factory->create(new TaskManagerDTO(
            $dto->id ?? $source->getId()->value,
            $dto->projectId ?? $source->getProjectId()->value,
            $dto->status ?? $source->getStatus()->getScalar(),
            $dto->ownerId ?? $source->getOwner()->userId->value,
            $dto->finishDate ?? $source->getFinishDate()->getValue(),
            $dto->participantIds ?? $source->getParticipants()->getInnerItems(),
            $dto->tasks ?? $source->getTasks()->getInnerItems()
        ));
    }
}
