<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Projects\Application\Query\GetAllOwnProjectsQueryResponse;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Pagination\Pagination;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;

final class GetAllOwnProjectsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ProjectQueryRepositoryInterface $projectRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @param GetAllOwnProjectsQuery $query
     * @return GetAllOwnProjectsQueryResponse
     */
    public function __invoke(GetAllOwnProjectsQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();

        $criteria = new Criteria([
            new ExpressionOperand('userId', '=', $userId->value)
        ]);
        $criteria->loadScalarFilters($query->criteria->filters);
        $count = $this->projectRepository->findCountByCriteria($criteria);

        $pagination = new Pagination(
            $count,
            $query->criteria->page
        );
        $criteria->loadScalarOrders($query->criteria->orders)
            ->loadOffsetAndLimit(...$pagination->getOffsetAndLimit());
        $projects = $this->projectRepository->findAllByCriteria($criteria);

        return new GetAllOwnProjectsQueryResponse(
            PaginationDTO::createFromPagination($pagination),
            ...$projects
        );
    }
}
