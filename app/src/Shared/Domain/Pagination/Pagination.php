<?php
declare(strict_types=1);

namespace App\Shared\Domain\Pagination;

use App\Shared\Domain\Exception\PageNotExistsException;

final class Pagination
{
    public const PAGE_SIZE = 10;

    public function __construct(
        private readonly int $totalCount,
        private readonly int $currentPage
    ) {
        $this->ensureIsValidCurrentPage();
    }

    public function getOffsetAndLimit(): array
    {
        $offset = ($this->currentPage - 1) * Pagination::PAGE_SIZE;
        $limit = Pagination::PAGE_SIZE;
        return [$offset, $limit];
    }

    public function getTotalPageCount(): int
    {
        return (int)ceil($this->totalCount / self::PAGE_SIZE);
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getNextPage(): ?int
    {
        $next = $this->getCurrentPage() + 1;
        return $next > $this->getTotalPageCount() ? null : $next;
    }

    public function getPrevPage(): ?int
    {
        $prev = $this->getCurrentPage() - 1;
        return $prev <= 0 ? null : $prev;
    }

    private function ensureIsValidCurrentPage(): void
    {
        if ($this->getTotalPageCount() === 0 and $this->currentPage === 1) {
            return;
        }
        if ($this->currentPage > $this->getTotalPageCount() || $this->currentPage < 1) {
            throw new PageNotExistsException($this->currentPage);
        }
    }
}
