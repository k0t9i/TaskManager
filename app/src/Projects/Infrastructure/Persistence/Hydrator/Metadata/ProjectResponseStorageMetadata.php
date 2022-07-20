<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\Entity\ProjectProjection;
use App\Shared\Application\Hydrator\Metadata\ResponseStorageMetadata;

final class ProjectResponseStorageMetadata extends ResponseStorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [
        'id' => 'id',
        'user_id' => 'userId',
        'name' => 'name',
        'description' => 'description',
        'finish_date' => 'finishDate',
        'owner_id' => 'ownerId',
        'owner_firstname' => 'ownerFirstname',
        'owner_lastname' => 'ownerLastname',
        'owner_email' => 'ownerEmail',
        'status' => 'status'
    ];

    public function getStorageName(): string
    {
        return 'project_projections';
    }

    public function getClassName(): string
    {
        return ProjectProjection::class;
    }
}
