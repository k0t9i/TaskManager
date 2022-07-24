<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginationItemsDTO;

interface PaginationResponseFormatterInterface
{
    public function format(PaginationItemsDTO $pagination): array;
}
