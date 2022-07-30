<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

final class RequestCriteriaDTO
{
    public function __construct(
        public readonly array $filters,
        public readonly array $orders,
        public readonly int $page
    ) {
    }
}
