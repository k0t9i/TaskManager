<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\Entity\ProjectListProjection;
use App\Shared\Application\Hydrator\Metadata\ProjectionStorageMetadata;

final class ProjectListProjectionStorageMetadata extends ProjectionStorageMetadata
{
    public function getStorageName(): string
    {
        return 'project_projections';
    }

    public function getClassName(): string
    {
        return ProjectListProjection::class;
    }
}
