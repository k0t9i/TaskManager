<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Hydrator\Metadata;

use App\Projects\Domain\Entity\Project;
use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StringValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\ChainValueMutator;
use App\Shared\Application\Hydrator\Mutator\DateValueMutator;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Application\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Application\Hydrator\Mutator\StringValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\ParticipantStorageMetadata;

final class ProjectStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'projects';
    }

    public function getClassName(): string
    {
        return Project::class;
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
                'name',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('name')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new StringValueMutator('name')
                )
            ),
            new StorageMetadataField(
                'description',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new StringValueAccessor('description')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new StringValueMutator('description')
                )
            ),
            new StorageMetadataField(
                'finish_date',
                new ChainValueAccessor(
                    new PropertyValueAccessor('information'),
                    new DateValueAccessor('finishDate')
                ),
                new ChainValueMutator(
                    new PropertyValueMutator('information'),
                    new DateValueMutator('finishDate')
                )
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
                new ChainValueMutator(
                    new PropertyValueMutator('tasks'),
                    new PropertyValueMutator('tasks')
                ),
                new ProjectTaskStorageMetadata()
            )
        ];
    }
}
