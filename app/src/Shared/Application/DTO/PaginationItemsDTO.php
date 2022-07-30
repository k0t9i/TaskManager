<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

use App\Shared\Application\Service\Pagination;

final class PaginationItemsDTO
{
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $current,
        public readonly ?int $prev,
        public readonly ?int $next
    ) {
    }

    public static function create(Pagination $pagination, array $items): self
    {
        return new self(
            $items,
            $pagination->getTotalPageCount(),
            $pagination->getCurrentPage(),
            $pagination->getPrevPage(),
            $pagination->getNextPage()
        );
    }
}
