<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;

final class SharedUserStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'shared_users';
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
                'email',
                new StringValueAccessor('email')
            ),
            new StorageMetadataField(
                'firstname',
                new StringValueAccessor('firstname')
            ),
            new StorageMetadataField(
                'lastname',
                new StringValueAccessor('lastname')
            )
        ];
    }
}
