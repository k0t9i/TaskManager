<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
use App\Shared\Domain\Pagination\Pagination;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Users\Application\Query\GetProjectUsersQuery;
use App\Users\Application\Query\GetProjectUsersQueryResponse;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;

final class GetProjectUsersQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserQueryRepositoryInterface $userRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
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
        $criteria->loadScalarFilters($query->criteria->filters);
        $count = $this->userRepository->findCountByCriteria($criteria);

        $pagination = new Pagination(
            $count,
            $query->criteria->page
        );
        $criteria->loadScalarOrders($query->criteria->orders)
            ->loadOffsetAndLimit(...$pagination->getOffsetAndLimit());
        $users = $this->userRepository->findAllByCriteria($criteria);

        return new GetProjectUsersQueryResponse(
            PaginationDTO::createFromPagination($pagination),
            ...$users
        );
    }
}
