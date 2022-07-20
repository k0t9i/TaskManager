<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Metadata\ResponseStorageMetadata;
use App\Users\Domain\Entity\UserProjection;

final class UserResponseStorageMetadata extends ResponseStorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [
        'user_id' => 'id',
        'project_id' => 'projectId',
        'owner_id' => 'ownerId',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'email'
    ];

    public function getStorageName(): string
    {
        return 'user_projections';
    }

    public function getClassName(): string
    {
        return UserProjection::class;
    }
}
