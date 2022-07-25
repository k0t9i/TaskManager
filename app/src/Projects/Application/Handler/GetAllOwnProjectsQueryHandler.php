<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Projects\Application\Query\GetAllOwnProjectsQueryResponse;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\PaginationBuilder;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;

final class GetAllOwnProjectsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ProjectQueryRepositoryInterface $projectRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
        private readonly PaginationBuilder $paginationBuilder
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

        $result = $this->paginationBuilder->build($this->projectRepository, $criteria, $query->criteria);

        return new GetAllOwnProjectsQueryResponse($result);
    }
}
