<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class RequestStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'requests';
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
                'user_id',
                new UuidValueAccessor('userId')
            ),
            new StorageMetadataField(
                'change_date',
                new DateValueAccessor('changeDate')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status')
            ),
            new StorageMetadataField(
                'request_manager_id',
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                )
            )
        ];
    }
}
