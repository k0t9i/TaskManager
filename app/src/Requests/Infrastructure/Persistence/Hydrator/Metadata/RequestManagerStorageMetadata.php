<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Requests\Domain\Entity\RequestManager;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\UuidValueMutator;

final class RequestManagerStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'request_managers';
    }

    public function getClassName(): string
    {
        return RequestManager::class;
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
                'project_id',
                new UuidValueAccessor('projectId'),
                new UuidValueMutator('projectId')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status'),
                new StatusValueMutator('status')
            ),
            new StorageMetadataField(
                'owner_id',
                new ChainValueAccessor(
                    new PropertyValueAccessor('owner'),
                    new UuidValueAccessor('userId')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('owner'),
                    new UuidValueMutator('userId')
                )
            ),
            new StorageMetadataField(
                'participants',
                new ChainValueAccessor(
                    new PropertyValueAccessor('participants'),
                    new PropertyValueAccessor('participants')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('participants'),
                    new PropertyValueMutator('participants')
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
                new ChainValueMutator(
                    new PropertyValueMutator('requests'),
                    new PropertyValueMutator('requests')
                ),
                new RequestStorageMetadata()
            )
        ];
    }
}
