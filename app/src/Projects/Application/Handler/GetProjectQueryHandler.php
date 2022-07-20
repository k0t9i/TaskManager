<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Query\GetProjectQuery;
use App\Projects\Application\Query\GetProjectQueryResponse;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserIsNotInProjectException;

final class GetProjectQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ProjectQueryRepositoryInterface $projectRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @param GetProjectQuery $query
     * @return GetProjectQueryResponse
     */
    public function __invoke(GetProjectQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $count = $this->projectRepository->findCountByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $query->id)
        ]));
        if ($count === 0) {
            throw new ProjectNotExistException($query->id);
        }

        $project = $this->projectRepository->findByCriteria(
            new Criteria([
                new ExpressionOperand('id', '=', $query->id),
                new ExpressionOperand('userId', '=', $userId->value),
            ])
        );
        if ($project === null) {
            throw new UserIsNotInProjectException($userId->value, $query->id);
        }

        return new GetProjectQueryResponse($project);
    }
}
