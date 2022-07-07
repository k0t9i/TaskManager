<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Controller;

use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Users\Application\Command\UpdateProfileCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'security.')]
final class UserController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    #[Route('/update-profile/', name: 'updateProfile', methods: ['PATCH'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->commandBus->dispatch(UpdateProfileCommand::createFromRequest($parameters));
        return new JsonResponse();
    }
}
