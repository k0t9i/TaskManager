<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Domain\Exception\PageNotExistException;

final class Pagination
{
    public const PAGE_SIZE = 10;

    public function __construct(
        private readonly int $totalCount = 0,
        private readonly int $currentPage = 0
    ) {
        $this->ensureIsValidCurrentPage();
    }

    public static function getOffsetAndLimit(int $page): array
    {
        $offset = ($page - 1) * Pagination::PAGE_SIZE;
        $limit = Pagination::PAGE_SIZE;
        return [$offset, $limit];
    }

    public function getTotalPageCount(): int
    {
        return (int) ceil($this->totalCount / self::PAGE_SIZE);
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
        if (0 === $this->getTotalPageCount() and 1 === $this->currentPage) {
            return;
        }
        if ($this->currentPage > $this->getTotalPageCount() || $this->currentPage < 1) {
            throw new PageNotExistException($this->currentPage);
        }
    }
}
