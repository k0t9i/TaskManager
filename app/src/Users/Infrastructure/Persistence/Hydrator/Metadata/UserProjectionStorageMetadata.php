<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Metadata\ProjectionStorageMetadata;
use App\Users\Domain\Entity\UserProjection;

final class UserProjectionStorageMetadata extends ProjectionStorageMetadata
{
    public function getStorageName(): string
    {
        return 'user_projections';
    }

    public function getClassName(): string
    {
        return UserProjection::class;
    }

    protected function columnToPropertyMap(): array
    {
        $map = parent::columnToPropertyMap();

        $map['user_id'] = $map['id'];
        unset($map['id']);

        return $map;
    }
}
