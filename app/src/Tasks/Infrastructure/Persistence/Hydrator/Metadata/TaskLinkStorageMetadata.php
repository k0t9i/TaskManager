<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\UuidValueMutator;
use App\Tasks\Domain\ValueObject\TaskLink;

final class TaskLinkStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'task_links';
    }

    public function getClassName(): string
    {
        return TaskLink::class;
    }

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        return [
            new StorageMetadataField(
                'to_task_id',
                new UuidValueAccessor('toTaskId'),
                new UuidValueMutator('toTaskId'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                'from_task_id',
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                ),
                isPrimaryKey: true,
                parentColumn: 'id'
            )
        ];
    }
}
