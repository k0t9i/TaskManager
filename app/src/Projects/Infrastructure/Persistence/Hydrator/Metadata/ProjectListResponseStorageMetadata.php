<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\PropertyValueMutator;

final class ProjectListResponseStorageMetadata extends StorageMetadata
{
    private const COLUMN_TO_PROPERTY_MAP = [
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

    private array $storageFields = [];

    public function getStorageName(): string
    {
        return 'project_projections';
    }

    public function getClassName(): string
    {
        return ProjectListResponseDTO::class;
    }

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        if (count($this->storageFields) === 0) {
            foreach (self::COLUMN_TO_PROPERTY_MAP as $column => $property) {
                $this->storageFields[] = new StorageMetadataField(
                    $column,
                    new PropertyValueAccessor($property),
                    new PropertyValueMutator($property)
                );
            }
        }
        return $this->storageFields;
    }
}
