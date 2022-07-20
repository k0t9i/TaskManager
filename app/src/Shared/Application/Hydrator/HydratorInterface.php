<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator;

use App\Shared\Application\Hydrator\DTO\HydratorEntityDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;

interface HydratorInterface
{
    public function loadIntoEntity(
        StorageMetadataInterface $metadata,
        HydratorEntityDTO $data
    ): object;
}
