<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class TaskManagerStorageMetadata extends StorageMetadata
{
    public function __construct()
    {
    }

    public function getStorageName(): string
    {
        return 'task_managers';
    }

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        return [
            new StorageMetadataField(
                'id',
                new UuidValueAccessor('id'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                'project_id',
                new UuidValueAccessor('projectId')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status')
            ),
            new StorageMetadataField(
                'owner_id',
                new ChainValueAccessor(
                    new PropertyValueAccessor('owner'),
                    new UuidValueAccessor('userId')
                ),
            ),
            new StorageMetadataField(
                'finish_date',
                new DateValueAccessor('finishDate')
            ),
            new StorageMetadataField(
                'participants',
                new ChainValueAccessor(
                    new PropertyValueAccessor('participants'),
                    new PropertyValueAccessor('participants')
                ),
                new ParticipantStorageMetadata(
                    'task_manager_participants',
                    'task_manager_id'
                )
            ),
            new StorageMetadataField(
                'tasks',
                new ChainValueAccessor(
                    new PropertyValueAccessor('tasks'),
                    new PropertyValueAccessor('tasks')
                ),
                new TaskStorageMetadata()
            )
        ];
    }
}
