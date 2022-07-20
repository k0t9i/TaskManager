<?php
declare(strict_types=1);

namespace App\Shared\Application\Storage;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Aggregate\AggregateRoot;

interface StorageSaverInterface
{
    public function insert(AggregateRoot $object, StorageMetadataInterface $metadata, bool $isVersioned = true): void;
    public function update(AggregateRoot $object, StorageMetadataInterface $metadata, ?int $prevVersion = null): void;
}
