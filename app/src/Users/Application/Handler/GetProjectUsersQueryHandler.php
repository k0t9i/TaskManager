<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\PaginationBuilder;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
use App\Users\Application\Query\GetProjectUsersQuery;
use App\Users\Application\Query\GetProjectUsersQueryResponse;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;

final class GetProjectUsersQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserQueryRepositoryInterface $userRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
        private readonly PaginationBuilder $paginationBuilder
    ) {
    }

    /**
     * @param GetProjectUsersQuery $query
     * @return GetProjectUsersQueryResponse
     */
    public function __invoke(GetProjectUsersQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $count = $this->userRepository->findCountByCriteria(new Criteria([
            new ExpressionOperand('projectId', '=', $query->projectId),
            new ExpressionOperand('id', '=', $userId->value)
        ]));
        if ($count === 0) {
            throw new UserIsNotInProjectException($userId->value, $query->projectId);
        }

        $criteria = new Criteria([
            new ExpressionOperand('projectId', '=', $query->projectId)
        ]);

        $result = $this->paginationBuilder->build($this->userRepository, $criteria, $query->criteria);

        return new GetProjectUsersQueryResponse($result);
    }
}
