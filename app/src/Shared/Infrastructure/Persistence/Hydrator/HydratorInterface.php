<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator;

use App\Shared\Infrastructure\Persistence\Hydrator\DTO\HydratorEntityDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

interface HydratorInterface
{
    public function loadIntoEntity(
        StorageMetadataInterface $metadata,
        HydratorEntityDTO $data
    ): object;
}
