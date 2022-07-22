<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Requests\Domain\Entity\RequestListProjection;
use App\Shared\Application\Hydrator\Metadata\ProjectionStorageMetadata;

final class RequestListProjectionStorageMetadata extends ProjectionStorageMetadata
{
    public function getStorageName(): string
    {
        return 'v_request_projections';
    }

    public function getClassName(): string
    {
        return RequestListProjection::class;
    }
}
