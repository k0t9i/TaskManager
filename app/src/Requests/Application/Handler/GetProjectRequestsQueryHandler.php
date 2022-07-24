<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Query\GetProjectRequestsQueryResponse;
use App\Requests\Application\Query\GetProjectsRequestsQuery;
use App\Requests\Domain\Repository\RequestQueryRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\PaginationBuilder;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;

final class GetProjectRequestsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly RequestQueryRepositoryInterface $repository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
        private readonly PaginationBuilder $paginationBuilder
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

        $result = $this->paginationBuilder->build($this->repository, $criteria, $query->criteria);

        return new GetProjectRequestsQueryResponse($result);
    }
}
