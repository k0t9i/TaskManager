<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Projects\Application\Query\GetAllOwnProjectsQueryResponse;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;

final class GetAllOwnProjectsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ProjectQueryRepositoryInterface $projectRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    /**
     * @param GetAllOwnProjectsQuery $query
     * @return GetAllOwnProjectsQueryResponse
     */
    public function __invoke(GetAllOwnProjectsQuery $query): QueryResponseInterface
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $projects = $this->projectRepository->findAllByUserId($userId);
        return new GetAllOwnProjectsQueryResponse(...$projects);
    }
}
