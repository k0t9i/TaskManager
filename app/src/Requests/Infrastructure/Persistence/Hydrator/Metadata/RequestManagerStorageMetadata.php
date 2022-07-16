<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class RequestManagerStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'request_managers';
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
                'project_id',
                new UuidValueAccessor('projectId')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status')
            ),
            new StorageMetadataField(
                'owner_id',
                new ChainValueAccessor(
                    new PropertyValueAccessor('owner'),
                    new UuidValueAccessor('userId')
                ),
            ),
            new StorageMetadataField(
                'participants',
                new ChainValueAccessor(
                    new PropertyValueAccessor('participants'),
                    new PropertyValueAccessor('participants')
                ),
                new ParticipantStorageMetadata(
                    'request_manager_participants',
                    'request_manager_id'
                )
            ),
            new StorageMetadataField(
                'requests',
                new ChainValueAccessor(
                    new PropertyValueAccessor('requests'),
                    new PropertyValueAccessor('requests')
                ),
                new RequestStorageMetadata()
            )
        ];
    }
}
