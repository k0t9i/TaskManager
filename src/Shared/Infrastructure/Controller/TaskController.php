<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Tasks\Application\Command\ActivateTaskCommand;
use App\Tasks\Application\Command\AddLinkCommand;
use App\Tasks\Application\Command\CloseTaskCommand;
use App\Tasks\Application\Command\DeleteLinkCommand;
use App\Tasks\Application\Command\UpdateTaskInformationCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks', name: 'task.')]
final class TaskController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
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
}
