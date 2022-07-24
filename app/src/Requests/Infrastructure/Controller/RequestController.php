<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Controller;

use App\Requests\Application\Command\ConfirmRequestCommand;
use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Requests\Application\Command\RejectRequestCommand;
use App\Requests\Application\Query\GetProjectRequestsQueryResponse;
use App\Requests\Application\Query\GetProjectsRequestsQuery;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\Service\PaginationResponseFormatterInterface;
use App\Shared\Application\Service\RequestCriteriaBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests', name: 'request.')]
final class RequestController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly RequestCriteriaBuilderInterface $criteriaBuilder,
        private readonly PaginationResponseFormatterInterface $responseFormatter
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
        $this->commandBus->dispatch(new RejectRequestCommand($id));

        return new JsonResponse();
    }

    #[Route('/in-project/{projectId}/', name: 'getAllInProject', methods: ['GET'])]
    public function getAllInProject(string $projectId, Request $request): JsonResponse
    {
        /** @var GetProjectRequestsQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(
            new GetProjectsRequestsQuery(
                $projectId,
                $this->criteriaBuilder->build($request->query->all())
            )
        );

        $response = $this->responseFormatter->format($envelope->getPagination());

        return new JsonResponse($response);
    }
}
