<?php

declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\TaskNotExistException;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
use App\Tasks\Application\Query\GetTaskQuery;
use App\Tasks\Application\Query\GetTaskQueryResponse;
use App\Tasks\Domain\Repository\TaskQueryRepositoryInterface;

final class GetTaskQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly TaskQueryRepositoryInterface $taskRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @return GetTaskQueryResponse
     */
    public function __invoke(GetTaskQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $task = $this->taskRepository->findByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $query->id),
        ]));
        if (null === $task) {
            throw new TaskNotExistException($query->id);
        }

        $userTask = $this->taskRepository->findByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $query->id),
            new ExpressionOperand('userId', '=', $userId->value),
        ]));
        if (null === $userTask) {
            throw new UserIsNotInProjectException($userId->value, $task->projectId);
        }

        return new GetTaskQueryResponse($task);
    }
}
