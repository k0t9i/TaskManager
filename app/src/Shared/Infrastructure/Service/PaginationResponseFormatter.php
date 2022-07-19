<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\DTO\PaginationDTO;

class PaginationResponseFormatter implements PaginationResponseFormatterInterface
{
    public function format(PaginationDTO $pagination, array $items): array
    {
        return [
            'page' => $pagination,
            'items' => $items
        ];
    }
}
