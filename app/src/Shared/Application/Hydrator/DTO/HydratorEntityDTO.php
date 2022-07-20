<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\DTO;

final class HydratorEntityDTO
{
    /**
     * @param string $table
     * @param array $columns
     * @param HydratorCollectionDTO $children
     */
    public function __construct(
        public readonly string $table,
        public readonly array $columns,
        public readonly ?HydratorCollectionDTO $children = null
    ) {
    }
}
