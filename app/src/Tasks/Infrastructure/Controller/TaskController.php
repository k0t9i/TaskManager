<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Controller;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\Service\PaginationResponseFormatterInterface;
use App\Shared\Application\Service\RequestCriteriaBuilderInterface;
use App\Tasks\Application\Command\ActivateTaskCommand;
use App\Tasks\Application\Command\AddLinkCommand;
use App\Tasks\Application\Command\CloseTaskCommand;
use App\Tasks\Application\Command\CreateTaskCommand;
use App\Tasks\Application\Command\DeleteLinkCommand;
use App\Tasks\Application\Command\UpdateTaskInformationCommand;
use App\Tasks\Application\Query\GetProjectTasksQuery;
use App\Tasks\Application\Query\GetProjectTasksQueryResponse;
use App\Tasks\Application\Query\GetTaskQuery;
use App\Tasks\Application\Query\GetTaskQueryResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks', name: 'task.')]
final class TaskController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly RequestCriteriaBuilderInterface $criteriaBuilder,
        private readonly PaginationResponseFormatterInterface $responseFormatter
    ) {
    }

    #[Route('/in-project/{projectId}/', name: 'createInProject', methods: ['POST'])]
    public function createInProject(string $projectId, Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->commandBus->dispatch(CreateTaskCommand::createFromRequest(
            $parameters,
            $projectId
        ));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route(
        '/in-project/{projectId}/create-for-participant/{participantId}/',
        name: 'createForParticipant',
        methods: ['POST']
    )]
    public function createTaskForParticipant(string $projectId, string $participantId, Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->commandBus->dispatch(CreateTaskCommand::createFromRequest(
            $parameters,
            $projectId,
            $participantId
        ));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/{id}/activate/', name: 'activate', methods: ['PATCH'])]
    public function activate(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new ActivateTaskCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/close/', name: 'close', methods: ['PATCH'])]
    public function close(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new CloseTaskCommand($id));

        return new JsonResponse();
    }

    #[Route('/{id}/', name: 'update', methods: ['PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $parameters['id'] = $id;

        $this->commandBus->dispatch(UpdateTaskInformationCommand::createFromRequest($parameters));

        return new JsonResponse();
    }

    #[Route('/{id}/add-link/{toTaskId}/', name: 'addLink', methods: ['PATCH'])]
    public function addLink(string $id, string $toTaskId): JsonResponse
    {
        $this->commandBus->dispatch(new AddLinkCommand($id, $toTaskId));

        return new JsonResponse();
    }

    #[Route('/{id}/delete-link/{toTaskId}/', name: 'deleteLink', methods: ['PATCH'])]
    public function deleteLink(string $id, string $toTaskId): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteLinkCommand($id, $toTaskId));

        return new JsonResponse();
    }

    #[Route('/in-project/{projectId}/', name: 'getAllInProject', methods: ['GET'])]
    public function getAllInProject(string $projectId, Request $request): JsonResponse
    {
        /** @var GetProjectTasksQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(
            new GetProjectTasksQuery(
                $projectId,
                $this->criteriaBuilder->build($request->query->all())
            )
        );

        $response = $this->responseFormatter->format($envelope->getPagination(), $envelope->getItems());

        return new JsonResponse($response);
    }

    #[Route('/{id}/', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        /** @var GetTaskQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetTaskQuery($id));

        return new JsonResponse($envelope->getTask());
    }
}
