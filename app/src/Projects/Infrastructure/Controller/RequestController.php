<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Controller;

use App\Projects\Application\Command\ConfirmRequestCommand;
use App\Projects\Application\Command\RejectRequestCommand;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests', name: 'request.')]
final class RequestController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
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
}
