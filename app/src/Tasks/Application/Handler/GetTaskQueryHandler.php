<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Exception\TaskNotExistException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
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
     * @param GetTaskQuery $query
     * @return GetTaskQueryResponse
     */
    public function __invoke(GetTaskQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $task = $this->taskRepository->findByIdAndUserId(
            new TaskId($query->id),
            $userId
        );
        if ($task === null) {
            throw new TaskNotExistException($query->id);
        }

        return new GetTaskQueryResponse($task);
    }
}
