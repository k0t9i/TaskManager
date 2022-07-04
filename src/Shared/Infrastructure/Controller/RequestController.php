<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Requests\Application\Command\ConfirmRequestCommand;
use App\Shared\Domain\Bus\Command\CommandBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/requests', name: 'request.')]
final class RequestController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
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
}
