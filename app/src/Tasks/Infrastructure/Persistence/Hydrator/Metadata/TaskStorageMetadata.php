<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Application\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Application\Hydrator\Mutator\DateValueMutator;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Application\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Application\Hydrator\Mutator\StringValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
use App\Tasks\Domain\Entity\Task;

final class TaskStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'tasks';
    }

    public function getClassName(): string
    {
        return Task::class;
    }

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        return [
            'id' => new StorageMetadataField(
                'id',
                new UuidValueAccessor('id'),
                new UuidValueMutator('id'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                'name',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('name')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new StringValueMutator('name')
                )
            ),
            new StorageMetadataField(
                'brief',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('brief')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new StringValueMutator('brief')
                )
            ),
            new StorageMetadataField(
                'description',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('description')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new StringValueMutator('description')
                )
            ),
            new StorageMetadataField(
                'start_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('startDate')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new DateValueMutator('startDate')
                )
            ),
            new StorageMetadataField(
                'finish_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('finishDate')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new DateValueMutator('finishDate')
                )
            ),
            new StorageMetadataField(
                'owner_id',
                new UuidValueAccessor('ownerId'),
                new UuidValueMutator('ownerId')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status'),
                new StatusValueMutator('status')
            ),
            new StorageMetadataField(
                'links',
                new PropertyValueAccessor('links'),
                new PropertyValueMutator('links'),
                new TaskLinkStorageMetadata()
            ),
            new StorageMetadataField(
                'task_manager_id',
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                ),
                parentColumn: 'id'
            )
        ];
    }
}
