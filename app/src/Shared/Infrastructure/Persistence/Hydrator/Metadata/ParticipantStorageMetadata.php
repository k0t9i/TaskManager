<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;

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

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        return [
            new StorageMetadataField(
                'user_id',
                new PropertyValueAccessor('value'),
                isPrimaryKey: true
            ),
            new StorageMetadataField(
                $this->parentFieldName,
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                ),
                isPrimaryKey: true
            )
        ];
    }
}
