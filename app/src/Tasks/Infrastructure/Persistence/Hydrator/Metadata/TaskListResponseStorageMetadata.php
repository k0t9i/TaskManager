<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Metadata\ResponseStorageMetadata;
use App\Tasks\Domain\Entity\TaskListProjection;

final class TaskListResponseStorageMetadata extends ResponseStorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [
        'id' => 'id',
        'user_id' => 'userId',
        'project_id' => 'projectId',
        'name' => 'name',
        'start_date' => 'startDate',
        'finish_date' => 'finishDate',
        'owner_id' => 'ownerId',
        'owner_firstname' => 'ownerFirstname',
        'owner_lastname' => 'ownerLastname',
        'owner_email' => 'ownerEmail',
        'status' => 'status',
        'links_count' => 'linksCount',
    ];

    public function getStorageName(): string
    {
        return 'v_task_projections';
    }

    public function getClassName(): string
    {
        return TaskListProjection::class;
    }
}
