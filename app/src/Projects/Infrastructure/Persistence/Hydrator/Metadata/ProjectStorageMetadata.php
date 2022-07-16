<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;

final class ProjectStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'projects';
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
                'name',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('name')
                ),
            ),
            new StorageMetadataField(
                'description',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('description')
                ),
            ),
            new StorageMetadataField(
                'finish_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('finishDate')
                ),
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
                    'project_participants',
                    'project_id',
                )
            ),
            new StorageMetadataField(
                'tasks',
                new ChainValueAccessor(
                    new PropertyValueAccessor('tasks'),
                    new PropertyValueAccessor('tasks')
                ),
                new ProjectTaskStorageMetadata()
            )
        ];
    }
}
