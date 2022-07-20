<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Shared\Application\Hydrator\Metadata\ResponseStorageMetadata;

final class ProjectListResponseStorageMetadata extends ResponseStorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [
        'id' => 'id',
        'user_id' => 'userId',
        'name' => 'name',
        'finish_date' => 'finishDate',
        'owner_id' => 'ownerId',
        'owner_firstname' => 'ownerFirstname',
        'owner_lastname' => 'ownerLastname',
        'owner_email' => 'ownerEmail',
        'status' => 'status',
        'tasks_count' => 'tasksCount',
        'participants_count' => 'participantsCount',
        'pending_requests_count' => 'pendingRequestsCount'
    ];

    public function getStorageName(): string
    {
        return 'project_projections';
    }

    public function getClassName(): string
    {
        return ProjectListResponseDTO::class;
    }
}
