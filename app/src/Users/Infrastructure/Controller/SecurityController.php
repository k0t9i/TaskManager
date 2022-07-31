<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Controller;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Users\Application\Service\LoginService;
use App\Users\Infrastructure\Symfony\DTO\UserRegisterDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/security', name: 'security.')]
final class SecurityController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly LoginService $loginService,
        private readonly UuidGeneratorInterface $uuidGenerator
    ) {
    }

    #[Route('/login/', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $token = $this->loginService->login(
            $parameters['email'],
            $parameters['password'],
        );

        return new JsonResponse([
            'token' => $token,
        ]);
    }

    #[Route('/register/', name: 'register', methods: ['POST'])]
    public function register(UserRegisterDTO $dto): JsonResponse
    {
        $command = $dto->createCommand($this->uuidGenerator->generate());
        $this->commandBus->dispatch($command);

        return new JsonResponse(['id' => $command->id], Response::HTTP_CREATED);
    }
}
