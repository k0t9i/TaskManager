<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator;

use App\Shared\Application\Hydrator\DTO\RehydratorEntityDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Aggregate\AggregateRoot;

interface RehydratorInterface
{
    public function loadFromAggregateRoot(
        AggregateRoot $aggregateRoot,
        StorageMetadataInterface $metadata
    ): RehydratorEntityDTO;
}
