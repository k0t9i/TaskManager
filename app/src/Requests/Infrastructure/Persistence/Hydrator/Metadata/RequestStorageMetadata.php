<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Hydrator\Metadata;

use App\Requests\Domain\Entity\Request;
use App\Shared\Application\Hydrator\Accessor\ChainValueAccessor;
use App\Shared\Application\Hydrator\Accessor\ConstValueAccessor;
use App\Shared\Application\Hydrator\Accessor\DateValueAccessor;
use App\Shared\Application\Hydrator\Accessor\StatusValueAccessor;
use App\Shared\Application\Hydrator\Accessor\UuidValueAccessor;
use App\Shared\Application\Hydrator\Metadata\StorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Application\Hydrator\Mutator\DateValueMutator;
use App\Shared\Application\Hydrator\Mutator\StatusValueMutator;
use App\Shared\Application\Hydrator\Mutator\UuidValueMutator;

final class RequestStorageMetadata extends StorageMetadata
{
    public function getStorageName(): string
    {
        return 'requests';
    }

    public function getClassName(): string
    {
        return Request::class;
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
                'user_id',
                new UuidValueAccessor('userId'),
                new UuidValueMutator('userId')
            ),
            new StorageMetadataField(
                'change_date',
                new DateValueAccessor('changeDate'),
                new DateValueMutator('changeDate')
            ),
            new StorageMetadataField(
                'status',
                new StatusValueAccessor('status'),
                new StatusValueMutator('status')
            ),
            new StorageMetadataField(
                'request_manager_id',
                new ChainValueAccessor(
                    new ConstValueAccessor($parentObject),
                    new UuidValueAccessor('id')
                ),
                parentColumn: 'id'
            )
        ];
    }
}
