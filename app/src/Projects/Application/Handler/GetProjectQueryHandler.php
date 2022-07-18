<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Query\GetProjectQuery;
use App\Projects\Application\Query\GetProjectQueryResponse;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserIsNotInProjectException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

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
        $project = $this->projectRepository->findById(new ProjectId($query->id));
        if ($project === null) {
            throw new ProjectNotExistException($query->id);
        }

        $project = $this->projectRepository->findByIdAndUserId(
            new ProjectId($query->id),
            $userId
        );
        if ($project === null) {
            throw new UserIsNotInProjectException($userId->value, $query->id);
        }

        return new GetProjectQueryResponse($project);
    }
}
