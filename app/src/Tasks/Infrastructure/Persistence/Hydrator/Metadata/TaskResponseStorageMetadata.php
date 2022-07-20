<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ResponseStorageMetadata;
use App\Tasks\Domain\DTO\TaskResponseDTO;

final class TaskResponseStorageMetadata extends ResponseStorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [
        'id' => 'id',
        'user_id' => 'userId',
        'project_id' => 'projectId',
        'name' => 'name',
        'brief' => 'brief',
        'description' => 'description',
        'start_date' => 'startDate',
        'finish_date' => 'finishDate',
        'owner_id' => 'ownerId',
        'owner_firstname' => 'ownerFirstname',
        'owner_lastname' => 'ownerLastname',
        'owner_email' => 'ownerEmail',
        'status' => 'status',
    ];

    public function getStorageName(): string
    {
        return 'v_task_projections';
    }

    public function getClassName(): string
    {
        return TaskResponseDTO::class;
    }
}
