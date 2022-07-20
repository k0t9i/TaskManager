<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ValueAccessorInterface;
use App\Shared\Infrastructure\Persistence\Hydrator\Mutator\ValueMutatorInterface;

final class StorageMetadataField
{
    public function __construct(
        public readonly string $name,
        public readonly ?ValueAccessorInterface $valueAccessor = null,
        public readonly ?ValueMutatorInterface $valueMutator = null,
        public readonly ?StorageMetadataInterface $metadata = null,
        public readonly bool $isPrimaryKey = false,
        public readonly ?string $parentColumn = null,
    ) {
    }
}
