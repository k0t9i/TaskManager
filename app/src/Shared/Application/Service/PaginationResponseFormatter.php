<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginationItemsDTO;

final class PaginationResponseFormatter implements PaginationResponseFormatterInterface
{
    public function format(PaginationItemsDTO $pagination): array
    {
        return [
            'page' => [
                'total' => $pagination->total,
                'current' => $pagination->current,
                'prev' => $pagination->prev,
                'next' => $pagination->next,
            ],
            'items' => $pagination->items,
        ];
    }
}
