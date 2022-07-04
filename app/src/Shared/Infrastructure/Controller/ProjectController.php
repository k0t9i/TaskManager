<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Projects\Application\Command\ActivateProjectCommand;
use App\Projects\Application\Command\ChangeProjectOwnerCommand;
use App\Projects\Application\Command\CloseProjectCommand;
use App\Projects\Application\Command\CreateProjectCommand;
use App\Projects\Application\Command\LeaveProjectCommand;
use App\Projects\Application\Command\RemoveProjectParticipantCommand;
use App\Projects\Application\Command\UpdateProjectInformationCommand;
use App\Projects\Application\Query\GetAllOwnProjectsQuery;
use App\Projects\Application\Query\GetAllOwnProjectsQueryResponse;
use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Shared\Domain\Bus\Query\QueryBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Tasks\Application\Command\CreateTaskCommand;
use App\Tasks\Application\Query\GetAllProjectTasksQuery;
use App\Tasks\Application\Query\GetAllProjectTasksQueryResponse;
use App\Users\Application\Query\GetUserQuery;
use App\Users\Application\Query\GetUserQueryResponse;
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
        private readonly AuthenticatorServiceInterface $authenticatorService
    ) {
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $ownerId = $this->authenticatorService->getAuthUser()->getId()->value;
        /** @var GetUserQueryResponse $owner */
        $owner = $this->queryBus->dispatch(new GetUserQuery($ownerId));

        $parameters = json_decode($request->getContent(), true);
        $parameters['owner_id'] = $owner->id;
        $parameters['owner_email'] = $owner->email;

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
        /** @var GetUserQueryResponse $owner */
        $owner = $this->queryBus->dispatch(new GetUserQuery($ownerId));

        $this->commandBus->dispatch(new ChangeProjectOwnerCommand($id, $owner->id, $owner->email));

        return new JsonResponse();
    }

    #[Route('/{id}/create-request/', name: 'createRequest', methods: ['POST'])]
    public function createRequest(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new CreateRequestToProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/leave/', name: 'leave', methods: ['PATCH'])]
    public function leave(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new LeaveProjectCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/remove-participant/{participantId}/', name: 'leave', methods: ['PATCH'])]
    public function removeParticipant(string $id, string $participantId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveProjectParticipantCommand($id, $participantId));

        return new JsonResponse();
    }

    #[Route('/{id}/create-task/', name: 'createTask', methods: ['POST'])]
    public function createTask(string $id, Request $request): JsonResponse
    {
        $ownerId = $this->authenticatorService->getAuthUser()->getId()->value;
        /** @var GetUserQueryResponse $owner */
        $owner = $this->queryBus->dispatch(new GetUserQuery($ownerId));

        $parameters = json_decode($request->getContent(), true);
        $parameters['project_id'] = $id;
        $parameters['owner_id'] = $owner->id;
        $parameters['owner_email'] = $owner->email;

        $this->commandBus->dispatch(CreateTaskCommand::createFromRequest($parameters));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/{id}/create-task-for-participant/{participantId}/', name: 'createTaskForParticipant', methods: ['POST'])]
    public function createTaskForParticipant(string $id, string $participantId, Request $request): JsonResponse
    {
        /** @var GetUserQueryResponse $owner */
        $owner = $this->queryBus->dispatch(new GetUserQuery($participantId));

        $parameters = json_decode($request->getContent(), true);
        $parameters['project_id'] = $id;
        $parameters['owner_id'] = $owner->id;
        $parameters['owner_email'] = $owner->email;

        $this->commandBus->dispatch(CreateTaskCommand::createFromRequest($parameters));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/', name: 'getAll', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        //TODO add paginator and ordering
        /** @var GetAllOwnProjectsQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetAllOwnProjectsQuery());

        return new JsonResponse($envelope->getProjects());
    }

    #[Route('/{id}/tasks/', name: 'getAllTasks', methods: ['GET'])]
    public function getAllTasks(string $id): JsonResponse
    {
        //TODO add paginator and ordering
        /** @var GetAllProjectTasksQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetAllProjectTasksQuery($id));

        return new JsonResponse($envelope->getTasks());
    }
}
