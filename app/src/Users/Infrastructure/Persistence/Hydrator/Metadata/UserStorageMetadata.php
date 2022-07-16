<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class UserStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'users';
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
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('firstname')
                ),
            ),
            new StorageMetadataField(
                'lastname',
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('lastname')
                ),
            ),
            new StorageMetadataField(
                'password',
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('password')
                ),
            )
        ];
    }
}
