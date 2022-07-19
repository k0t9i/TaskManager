<?php
declare(strict_types=1);

namespace App\Shared\Application\DTO;

use App\Shared\Domain\Pagination\Pagination;

final class PaginationDTO
{
    public function __construct(
        public readonly int $total,
        public readonly int $current,
        public readonly ?int $prev,
        public readonly ?int $next
    ) {
    }

    public static function createFromPagination(Pagination $pagination): self
    {
        return new self(
            $pagination->getTotalPageCount(),
            $pagination->getCurrentPage(),
            $pagination->getPrevPage(),
            $pagination->getNextPage()
        );
    }
}
