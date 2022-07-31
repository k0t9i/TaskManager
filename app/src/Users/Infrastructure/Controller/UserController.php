<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Controller;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\Shared\Application\DTO\RequestCriteriaDTO;
use App\Shared\Application\Service\PaginationResponseFormatterInterface;
use App\Users\Application\Query\GetProfileQuery;
use App\Users\Application\Query\GetProfileQueryResponse;
use App\Users\Application\Query\GetProjectUsersQuery;
use App\Users\Application\Query\GetProjectUsersQueryResponse;
use App\Users\Infrastructure\Symfony\DTO\ProfileUpdateDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'security.')]
final class UserController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly PaginationResponseFormatterInterface $responseFormatter
    ) {
    }

    #[Route('/profile/', name: 'updateProfile', methods: ['PATCH'])]
    public function updateProfile(ProfileUpdateDTO $dto): JsonResponse
    {
        $this->commandBus->dispatch($dto->createCommand());

        return new JsonResponse();
    }

    #[Route('/profile/', name: 'getProfile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        /** @var GetProfileQueryResponse $envelop */
        $envelop = $this->queryBus->dispatch(new GetProfileQuery());

        return new JsonResponse($envelop->getProfile());
    }

    #[Route('/in-project/{id}/', name: 'getAllInProject', methods: ['GET'])]
    public function getAllInProject(string $id, RequestCriteriaDTO $dto): JsonResponse
    {
        /** @var GetProjectUsersQueryResponse $envelope */
        $envelope = $this->queryBus->dispatch(new GetProjectUsersQuery($id, $dto));

        $response = $this->responseFormatter->format($envelope->getPagination());

        return new JsonResponse($response);
    }
}
