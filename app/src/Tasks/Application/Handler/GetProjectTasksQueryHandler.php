<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Tasks\Application\Query\GetProjectTasksQuery;
use App\Tasks\Application\Query\GetProjectTasksQueryResponse;
use App\Tasks\Domain\Repository\TaskQueryRepositoryInterface;

final class GetProjectTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly TaskQueryRepositoryInterface $taskRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @param GetProjectTasksQuery $query
     * @return GetProjectTasksQueryResponse
     */
    public function __invoke(GetProjectTasksQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $tasks = $this->taskRepository->findAllByCriteria(new Criteria([
            new ExpressionOperand('projectId', '=', $query->projectId),
            new ExpressionOperand('userId', '=', $userId->value)
        ]));
        return new GetProjectTasksQueryResponse(...$tasks);
    }
}
