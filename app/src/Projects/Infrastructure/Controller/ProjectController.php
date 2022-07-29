<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Controller;

use App\Projects\Application\Command\ActivateProjectCommand;
use App\Projects\Application\Command\ChangeProjectOwnerCommand;
use App\Projects\Application\Command\CloseProjectCommand;
use App\Projects\Application\Command\CreateProjectCommand;
use App\Projects\Application\Command\CreateRequestToProjectCommand;
use App\Projects\Application\Command\LeaveProjectCommand;
use App\Projects\Application\Command\RemoveProjectParticipantCommand;
use App\Projects\Application\Command\UpdateProjectInformationCommand;
use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Projects\Application\Query\GetAllOwnProjectsQueryResponse;
use App\Projects\Application\Query\GetProjectQuery;
use App\Projects\Application\Query\GetProjectQueryResponse;
use App\Projects\Application\Query\GetProjectRequestsQueryResponse;
use App\Projects\Application\Query\GetProjectsRequestsQuery;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\Service\PaginationResponseFormatterInterface;
use App\Shared\Application\Service\RequestCriteriaBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/projects', name: 'project.')]
final class ProjectController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly RequestCriteriaBuilderInterface $criteriaBuilder,
        private readonly PaginationResponseFormatterInterface $responseFormatter
    ) {
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->commandBus->dispatch(CreateProjectCommand::createFromRequest($parameters));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/{id}/activate/', name: 'activate', methods: ['PATCH'])]
    public function activate(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new ActivateProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/close/', name: 'close', methods: ['PATCH'])]
    public function close(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new CloseProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/', name: 'update', methods: ['PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $parameters['id'] = $id;

        $this->commandBus->dispatch(UpdateProjectInformationCommand::createFromRequest($parameters));

        return new JsonResponse();
    }

    #[Route('/{id}/change-owner/{ownerId}/', name: 'changeOwner', methods: ['PATCH'])]
    public function changeOwner(string $id, string $ownerId): JsonResponse
    {
        $this->commandBus->dispatch(new ChangeProjectOwnerCommand($id, $ownerId));

        return new JsonResponse();
    }

    #[Route('/{id}/leave/', name: 'leave', methods: ['PATCH'])]
    public function leave(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new LeaveProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/remove-participant/{participantId}/', name: 'removeParticipant', methods: ['PATCH'])]
    public function removeParticipant(string $id, string $participantId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveProjectParticipantCommand($id, $participantId));

        return new JsonResponse();
    }

    #[Route('/{id}/requests/', name: 'createRequest', methods: ['POST'])]
    public function createRequest(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new CreateRequestToProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/', name: 'getAll', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        /** @var GetAllOwnProjectsQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(
            new GetAllOwnProjectsQuery(
                $this->criteriaBuilder->build($request->query->all())
            )
        );

        $response = $this->responseFormatter->format($envelope->getPagination());

        return new JsonResponse($response);
    }

    #[Route('/{id}/', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        /** @var GetProjectQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetProjectQuery($id));

        return new JsonResponse($envelope->getProject());
    }

    #[Route('/{id}/requests/', name: 'getAllRequests', methods: ['GET'])]
    public function getAllRequests(string $id, Request $request): JsonResponse
    {
        /** @var GetProjectRequestsQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(
            new GetProjectsRequestsQuery(
                $id,
                $this->criteriaBuilder->build($request->query->all())
            )
        );

        $response = $this->responseFormatter->format($envelope->getPagination());

        return new JsonResponse($response);
    }
}
