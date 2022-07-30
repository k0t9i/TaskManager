<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\PaginationItemsDTO;
use App\Shared\Application\DTO\RequestCriteriaDTO;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;

final class PaginationBuilder implements PaginationBuilderInterface
{
    public function build(
        PageableRepositoryInterface $repository,
        Criteria $criteria,
        RequestCriteriaDTO $dto
    ): PaginationItemsDTO {
        $criteria->loadScalarFilters($dto->filters)
            ->loadScalarOrders($dto->orders)
            ->loadOffsetAndLimit(...Pagination::getOffsetAndLimit($dto->page));
        $count = $repository->findCountByCriteria($criteria);
        $items = $repository->findAllByCriteria($criteria);

        return PaginationItemsDTO::create(new Pagination($count, $dto->page), $items);
    }
}
