<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginationItemsDTO;
use App\Shared\Application\DTO\RequestCriteriaDTO;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;

interface PaginationBuilderInterface
{
    public function build(
        PageableRepositoryInterface $repository,
        Criteria $criteria,
        RequestCriteriaDTO $dto
    ): PaginationItemsDTO;
}
