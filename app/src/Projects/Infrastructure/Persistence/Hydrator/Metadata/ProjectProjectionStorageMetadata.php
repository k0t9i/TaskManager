<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\Entity\ProjectProjection;
use App\Shared\Application\Hydrator\Metadata\ProjectionStorageMetadata;

final class ProjectProjectionStorageMetadata extends ProjectionStorageMetadata
{
    public function getStorageName(): string
    {
        return 'project_projections';
    }

    public function getClassName(): string
    {
        return ProjectProjection::class;
    }
}
