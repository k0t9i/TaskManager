<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Domain\ValueObject\Users\UserId;

final class ParticipantStorageMetadata extends StorageMetadata
{
    public function __construct(
        private readonly string $storageName,
        private readonly string $parentFieldName
    ) {
    }

    public function getStorageName(): string
    {
        return $this->storageName;
    }

    public function getClassName(): string
    {
        return UserId::class;
    }

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        return [
            new StorageMetadataField(
                'user_id',
                new PropertyValueAccessor('value'),
                new PropertyValueMutator('value'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                $this->parentFieldName,
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
