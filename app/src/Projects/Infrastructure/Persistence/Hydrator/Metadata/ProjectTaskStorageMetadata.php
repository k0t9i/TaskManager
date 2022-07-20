<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\Entity\ProjectTask;
use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;

final class ProjectTaskStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'project_tasks';
    }

    public function getClassName(): string
    {
        return ProjectTask::class;
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
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                ),
                parentColumn: 'id'
            ),
            new StorageMetadataField(
                'task_id',
                new UuidValueAccessor('taskId'),
                new UuidValueMutator('taskId')
            ),
            new StorageMetadataField(
                'owner_id',
                new UuidValueAccessor('ownerId'),
                new UuidValueMutator('ownerId')
            ),
        ];
    }
}
