<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Controller;

use App\Requests\Application\Command\ConfirmRequestCommand;
use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Requests\Application\Query\GetAllProjectRequestsQuery;
use App\Requests\Application\Query\GetAllProjectRequestsQueryResponse;
use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Shared\Domain\Bus\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests', name: 'request.')]
final class RequestController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/in-project/{projectId}/', name: 'createInProject', methods: ['POST'])]
    public function createInProject(string $projectId): JsonResponse
    {
        $this->commandBus->dispatch(new CreateRequestToProjectCommand($projectId));

        return new JsonResponse();
    }

    #[Route('/{id}/confirm/', name: 'confirm', methods: ['PATCH'])]
    public function confirm(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new ConfirmRequestCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/reject/', name: 'reject', methods: ['PATCH'])]
    public function reject(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new ConfirmRequestCommand($id));

        return new JsonResponse();
    }

    #[Route('/in-project/{projectId}/', name: 'getAllInProject', methods: ['GET'])]
    public function getAllInProject(string $projectId): JsonResponse
    {
        //TODO add paginator and ordering
        /** @var GetAllProjectRequestsQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetAllProjectRequestsQuery($projectId));

        return new JsonResponse($envelope->getRequests());
    }
}
