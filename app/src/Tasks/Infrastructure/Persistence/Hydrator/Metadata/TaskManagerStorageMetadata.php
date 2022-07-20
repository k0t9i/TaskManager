<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Application\Hydrator\Mutator\DateValueMutator;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Application\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;
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
            'projectId' => new StorageMetadataField(
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
            'tasks' => new StorageMetadataField(
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
