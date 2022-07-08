<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Factory;

use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\DTO\TaskMergeDTO;
use App\Tasks\Domain\Entity\Task;

final class TaskMerger
{
    public function __construct(private readonly TaskFactory $factory)
    {
    }

    public function merge(Task $source, TaskMergeDTO $dto): Task
    {
        $info = $source->getInformation();
        return $this->factory->create(new TaskDTO(
            $dto->id ?? $source->getId()->value,
            $dto->name ?? $info->name->value,
            $dto->brief ?? $info->brief->value,
            $dto->description ?? $info->description->value,
            $dto->startDate ?? $info->startDate->getValue(),
            $dto->finishDate ?? $info->finishDate->getValue(),
            $dto->ownerId ?? $source->getOwnerId()->value,
            $dto->status ?? $source->getStatus()->getScalar(),
            $dto->links ?? $source->getLinks()
        ));
    }
}
