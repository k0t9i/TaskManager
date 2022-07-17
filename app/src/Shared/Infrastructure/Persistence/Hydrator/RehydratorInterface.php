<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Infrastructure\Persistence\Hydrator\DTO\RehydratorEntityDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

interface RehydratorInterface
{
    public function loadFromAggregateRoot(
        AggregateRoot $aggregateRoot,
        StorageMetadataInterface $metadata
    ): RehydratorEntityDTO;
}
