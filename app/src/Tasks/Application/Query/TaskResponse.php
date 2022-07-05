<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Tasks\Domain\Entity\Task;

final class TaskResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly int $status
    ) {
    }

    public static function createFromEntity(Task $task): self
    {
        return new self(
            $task->getId()->value,
            $task->getInformation()->name->value,
            $task->getInformation()->startDate->getValue(),
            $task->getInformation()->finishDate->getValue(),
            $task->getOwnerId()->value,
            $task->getStatus()->getScalar()
        );
    }
}
