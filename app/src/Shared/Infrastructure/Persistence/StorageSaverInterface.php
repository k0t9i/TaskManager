<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

interface StorageSaverInterface
{
    public function insert(AggregateRoot $object, StorageMetadataInterface $metadata): void;
    public function update(AggregateRoot $object, StorageMetadataInterface $metadata, ?int $prevVersion = null): void;
}
