<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Requests\Domain\Entity\RequestManager;
use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Application\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;

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
            'id' => new StorageMetadataField(
                'id',
                new UuidValueAccessor('id'),
                new UuidValueMutator('id'),
                isPrimaryKey: true
            ),
            'projectId' => new StorageMetadataField(
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
            'requests' => new StorageMetadataField(
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
