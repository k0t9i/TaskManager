<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

use App\Shared\Infrastructure\Persistence\Hydrator\Accessor\ValueAccessorInterface;

final class StorageMetadataField
{
    public function __construct(
        public readonly string $name,
        public readonly ValueAccessorInterface $valueAccessor,
        public readonly ?StorageMetadataInterface $metadata = null,
        public readonly bool $isPrimaryKey = false
    ) {
    }
}
