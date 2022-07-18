<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
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
        $user = $this->userRepository->findByProjectIdAndUserId(
            new ProjectId($query->projectId),
            $userId
        );
        if ($user === null) {
            throw new UserIsNotInProjectException($userId->value, $query->projectId);
        }

        $users = $this->userRepository->findAllByProjectId(new ProjectId($query->projectId));

        return new GetProjectUsersQueryResponse(...$users);
    }
}
