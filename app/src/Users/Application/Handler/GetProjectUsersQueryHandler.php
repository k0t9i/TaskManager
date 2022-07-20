<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
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
        $user = $this->userRepository->findByCriteria(new Criteria([
            new ExpressionOperand('project_id', '=', $query->projectId),
            new ExpressionOperand('user_id', '=', $userId->value)
        ]));
        if ($user === null) {
            throw new UserIsNotInProjectException($userId->value, $query->projectId);
        }

        $users = $this->userRepository->findAllByCriteria(new Criteria([
            new ExpressionOperand('project_id', '=', $query->projectId)
        ]));

        return new GetProjectUsersQueryResponse(...$users);
    }
}
