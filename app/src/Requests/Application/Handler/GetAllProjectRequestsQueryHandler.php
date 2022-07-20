<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Query\GetAllProjectRequestsQuery;
use App\Requests\Application\Query\GetAllProjectRequestsQueryResponse;
use App\Requests\Application\Query\RequestResponse;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Application\Bus\Query\QueryHandlerInterface;
use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

final class GetAllProjectRequestsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly AuthenticatorServiceInterface $authenticatorService,
    ) {
    }

    /**
     * @param GetAllProjectRequestsQuery $query
     * @return GetAllProjectRequestsQueryResponse
     */
    public function __invoke(GetAllProjectRequestsQuery $query): QueryResponseInterface
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($query->projectId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $result = [];
        $userId = $this->authenticatorService->getAuthUser()->getId();
        foreach ($manager->getRequestsForOwner($userId) as $request) {
            $result[] = RequestResponse::createFromEntity($request);
        }
        return new GetAllProjectRequestsQueryResponse(...$result);
    }
}
