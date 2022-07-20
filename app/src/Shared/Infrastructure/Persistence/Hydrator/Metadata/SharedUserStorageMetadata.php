<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\StringValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\UuidValueMutator;

final class SharedUserStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'shared_users';
    }

    public function getClassName(): string
    {
        return SharedUser::class;
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
                'email',
                new StringValueAccessor('email'),
                new StringValueMutator('email')
            ),
            new StorageMetadataField(
                'firstname',
                new StringValueAccessor('firstname'),
                new StringValueMutator('firstname')
            ),
            new StorageMetadataField(
                'lastname',
                new StringValueAccessor('lastname'),
                new StringValueMutator('lastname')
            )
        ];
    }
}
