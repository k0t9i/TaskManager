<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\StringValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\UuidValueMutator;
use App\Users\Domain\Entity\User;

final class UserStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'users';
    }

    public function getClassName(): string
    {
        return User::class;
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
                'email',
                new StringValueAccessor('email'),
                new StringValueMutator('email')
            ),
            new StorageMetadataField(
                'firstname',
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('firstname')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('profile'),
                    new StringValueMutator('firstname')
                )
            ),
            new StorageMetadataField(
                'lastname',
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('lastname')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('profile'),
                    new StringValueMutator('lastname')
                )
            ),
            new StorageMetadataField(
                'password',
                new ChainValueAccessor(
                    new PropertyValueAccessor('profile'),
                    new StringValueAccessor('password')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('profile'),
                    new StringValueMutator('password')
                )
            )
        ];
    }
}
