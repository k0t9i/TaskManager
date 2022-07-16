<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\DTO;

final class RehydratorCollectionDTO
{
    public function __construct(
        public readonly array $added,
        public readonly array $updated,
        public readonly array $deleted
    ) {
    }
}
