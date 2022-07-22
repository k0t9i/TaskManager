<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Metadata\ProjectionStorageMetadata;
use App\Tasks\Domain\Entity\TaskListProjection;

final class TaskListProjectionStorageMetadata extends ProjectionStorageMetadata
{
    public function getStorageName(): string
    {
        return 'v_task_projections';
    }

    public function getClassName(): string
    {
        return TaskListProjection::class;
    }
}
