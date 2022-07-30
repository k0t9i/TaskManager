<?php

declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\PaginationBuilder;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Tasks\Application\Query\GetTaskLinksQuery;
use App\Tasks\Application\Query\GetTaskLinksQueryResponse;
use App\Tasks\Domain\Repository\TaskLinkQueryRepositoryInterface;

final class GetTaskLinksQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly TaskLinkQueryRepositoryInterface $repository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
        private readonly PaginationBuilder $paginationBuilder
    ) {
    }

    /**
     * @return GetTaskLinksQueryResponse
     */
    public function __invoke(GetTaskLinksQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();

        $criteria = new Criteria([
            new ExpressionOperand('ownerTaskId', '=', $query->id),
            new ExpressionOperand('userId', '=', $userId->value),
        ]);
        $result = $this->paginationBuilder->build($this->repository, $criteria, $query->criteria);

        return new GetTaskLinksQueryResponse($result);
    }
}
