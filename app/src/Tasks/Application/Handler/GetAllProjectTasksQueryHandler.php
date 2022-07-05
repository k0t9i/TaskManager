<?php
declare(strict_types=1);

namespace App\Tasks\Application\Handler;

use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Tasks\Application\Query\GetAllProjectTasksQuery;
use App\Tasks\Application\Query\GetAllProjectTasksQueryResponse;
use App\Tasks\Application\Query\TaskResponse;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

final class GetAllProjectTasksQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
    ) {
    }

    /**
     * @param GetAllOwnProjectsQuery $query
     * @return GetAllProjectTasksQueryResponse
     */
    public function __invoke(GetAllProjectTasksQuery $query): QueryResponseInterface
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($query->projectId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $result = [];
        $userId = $this->authenticatorService->getAuthUser()->getId();
        foreach ($manager->getTasksForProjectUser($userId) as $task) {
            $result[] = TaskResponse::createFromEntity($task);
        }
        return new GetAllProjectTasksQueryResponse(...$result);
    }
}
