<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\DTO;

final class RehydratorEntityDTO
{
    public function __construct(
        public readonly string $table,
        public readonly array $primaryKey,
        public readonly array $columns,
        public readonly ?RehydratorCollectionDTO $children
    ) {
    }
}
