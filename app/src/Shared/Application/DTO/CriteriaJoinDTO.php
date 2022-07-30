<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

final class CriteriaJoinDTO
{
    public function __construct(
        public readonly string $parentAlias,
        public readonly string $joinTable,
        public readonly string $joinAlias,
        public readonly string $condition
    ) {
    }
}
