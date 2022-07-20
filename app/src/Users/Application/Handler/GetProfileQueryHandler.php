<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Users\Application\Query\GetProfileQuery;
use App\Users\Application\Query\GetProfileQueryResponse;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;

final class GetProfileQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserQueryRepositoryInterface $userRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @param GetProfileQuery $query
     * @return GetProfileQueryResponse
     */
    public function __invoke(GetProfileQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $user = $this->userRepository->findProfileByCriteria(new Criteria([
            new ExpressionOperand('user_id', '=', $userId->value)
        ]));
        if ($user === null) {
            throw new UserNotExistException($userId->value);
        }

        return new GetProfileQueryResponse($user);
    }
}
