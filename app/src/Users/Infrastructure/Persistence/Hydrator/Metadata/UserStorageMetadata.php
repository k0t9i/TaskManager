<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Application\Hydrator\Mutator\StringValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
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
            'id' => new StorageMetadataField(
                'id',
                new UuidValueAccessor('id'),
                new UuidValueMutator('id'),
                isPrimaryKey: true
            ),
            'email' => new StorageMetadataField(
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
