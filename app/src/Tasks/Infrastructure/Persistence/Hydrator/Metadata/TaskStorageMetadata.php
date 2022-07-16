<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class TaskStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'tasks';
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
                'name',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('name')
                ),
            ),
            new StorageMetadataField(
                'name',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('brief')
                ),
            ),
            new StorageMetadataField(
                'description',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('description')
                ),
            ),
            new StorageMetadataField(
                'start_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('startDate')
                ),
            ),
            new StorageMetadataField(
                'finish_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('finishDate')
                ),
            ),
            new StorageMetadataField(
                'owner_id',
                new UuidValueAccessor('ownerId'),
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status')
            ),
            new StorageMetadataField(
                'links',
                new PropertyValueAccessor('links'),
                new TaskLinkStorageMetadata()
            )
        ];
    }
}
