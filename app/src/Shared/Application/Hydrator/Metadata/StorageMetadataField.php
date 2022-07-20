<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\ValueAccessorInterface;
use App\Shared\Application\Hydrator\Mutator\ValueMutatorInterface;

final class StorageMetadataField
{
    public function __construct(
        public readonly string $name,
        public readonly ValueAccessorInterface $valueAccessor,
        public readonly ?ValueMutatorInterface $valueMutator = null,
        public readonly ?StorageMetadataInterface $metadata = null,
        public readonly bool $isPrimaryKey = false,
        public readonly ?string $parentColumn = null,
    ) {
    }
}
