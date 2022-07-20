<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginationDTO;

interface PaginationResponseFormatterInterface
{
    public function format(PaginationDTO $pagination, array $items): array;
}
