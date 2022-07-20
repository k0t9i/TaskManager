<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\StringValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
use App\Shared\Domain\Entity\SharedUser;

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
