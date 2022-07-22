<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Query\GetProjectRequestsQueryResponse;
use App\Requests\Application\Query\GetProjectsRequestsQuery;
use App\Requests\Domain\Repository\RequestQueryRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\Pagination;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;

final class GetProjectRequestsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly RequestQueryRepositoryInterface $repository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
    ) {
    }

    /**
     * @param GetProjectsRequestsQuery $query
     * @return GetProjectRequestsQueryResponse
     */
    public function __invoke(GetProjectsRequestsQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();

        $criteria = new Criteria([
            new ExpressionOperand('projectId', '=', $query->projectId),
            new ExpressionOperand('projectOwnerId', '=', $userId->value)
        ]);
        $criteria->loadScalarFilters($query->criteria->filters);
        $count = $this->repository->findCountByCriteria($criteria);

        $pagination = new Pagination(
            $count,
            $query->criteria->page
        );
        $criteria->loadScalarOrders($query->criteria->orders)
            ->loadOffsetAndLimit(...$pagination->getOffsetAndLimit());
        $requests = $this->repository->findAllByCriteria($criteria);

        return new GetProjectRequestsQueryResponse(
            PaginationDTO::createFromPagination($pagination),
            ...$requests
        );
    }
}
