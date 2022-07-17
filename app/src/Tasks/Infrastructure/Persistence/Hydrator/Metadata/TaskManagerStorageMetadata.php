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
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\DateValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\UuidValueMutator;
use App\Tasks\Domain\Entity\TaskManager;

final class TaskManagerStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'task_managers';
    }

    public function getClassName(): string
    {
        return TaskManager::class;
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
                new UuidValueMutator('id'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                'project_id',
                new UuidValueAccessor('projectId'),
                new UuidValueMutator('projectId'),
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status'),
                new StatusValueMutator('status')
            ),
            new StorageMetadataField(
                'owner_id',
                new ChainValueAccessor(
                    new PropertyValueAccessor('owner'),
                    new UuidValueAccessor('userId')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('owner'),
                    new UuidValueMutator('userId')
                )
            ),
            new StorageMetadataField(
                'finish_date',
                new DateValueAccessor('finishDate'),
                new DateValueMutator('finishDate')
            ),
            new StorageMetadataField(
                'participants',
                new ChainValueAccessor(
                    new PropertyValueAccessor('participants'),
                    new PropertyValueAccessor('participants')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('participants'),
                    new PropertyValueMutator('participants')
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
                new ChainValueMutator(
                    new PropertyValueMutator('tasks'),
                    new PropertyValueMutator('tasks')
                ),
                new TaskStorageMetadata()
            )
        ];
    }
}
